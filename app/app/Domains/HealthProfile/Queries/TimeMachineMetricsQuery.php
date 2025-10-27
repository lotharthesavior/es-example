<?php

namespace App\Domains\HealthProfile\Queries;

use App\Domains\HealthProfile\Events\MetricDeleted;
use App\Domains\HealthProfile\Events\MetricRecorded;
use App\Domains\HealthProfile\Projections\Metric;
use App\Enums\MetricType;
use App\Models\StoredEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimeMachineMetricsQuery
{
    protected ?Carbon $untilDateTime = null;
    protected ?string $profileUuid = null;
    protected ?MetricType $metricType = null;
    protected ?string $metricUuid = null;
    protected array $metricUuids = [];
    protected bool $includeDeleted = false;

    /**
     * Set the point in time to query until (inclusive)
     */
    public function until(Carbon|string $dateTime): self
    {
        $this->untilDateTime = $dateTime instanceof Carbon
            ? $dateTime
            : Carbon::parse($dateTime);
        return $this;
    }

    /**
     * Filter by profile UUID
     */
    public function forProfile(string $profileUuid): self
    {
        $this->profileUuid = $profileUuid;
        return $this;
    }

    /**
     * Filter by metric type
     */
    public function ofType(MetricType|string $type): self
    {
        $this->metricType = $type instanceof MetricType
            ? $type
            : MetricType::from($type);
        return $this;
    }

    /**
     * Filter by specific metric UUID
     */
    public function forMetric(string $metricUuid): self
    {
        $this->metricUuid = $metricUuid;
        return $this;
    }

    /**
     * Filter by multiple metric UUIDs
     */
    public function forMetrics(array $metricUuids): self
    {
        $this->metricUuids = $metricUuids;
        return $this;
    }

    /**
     * Include metrics that were deleted before the point in time
     */
    public function includeDeleted(): self
    {
        $this->includeDeleted = true;
        return $this;
    }

    /**
     * Execute the query and replay events to reconstruct metrics state in the database
     */
    public function get(): Collection
    {
        $events = $this->queryStoredEvents();
        return $this->replayEvents($events);
    }

    /**
     * Query the stored_events table with filters
     */
    protected function queryStoredEvents(): Collection
    {
        $query = StoredEvent::query()
            ->whereIn('event_class', [
                MetricRecorded::class,
                MetricDeleted::class,
            ])
            ->orderBy('created_at')
            ->orderBy('id');

        if ($this->untilDateTime) {
            $query->where('created_at', '<=', $this->untilDateTime);
        }

        if ($this->profileUuid) {
            $query->whereRaw(
                "JSON_EXTRACT(event_properties, '$.profileUuid') = ?",
                [$this->profileUuid]
            );
        }

        if ($this->metricType) {
            $query->whereRaw(
                "JSON_EXTRACT(event_properties, '$.type') = ?",
                [$this->metricType->value]
            );
        }

        if ($this->metricUuid) {
            $query->where('aggregate_uuid', $this->metricUuid);
        }

        if (!empty($this->metricUuids)) {
            $query->whereIn('aggregate_uuid', $this->metricUuids);
        }

        return $query->get();
    }

    /**
     * Replay events to reconstruct the state at the point in time
     */
    protected function replayEvents(Collection $events): Collection
    {
        Metric::truncate();

        $metrics = collect();

        DB::transaction(function () use ($events, &$metrics) {
            foreach ($events as $storedEvent) {
                $actualEvent = $storedEvent->toStoredEvent();
                $actualEvent->handle(); // Let Metric projector handle persistence

                $event = $actualEvent->event;
                $metricUuid = $storedEvent->aggregate_uuid;

                // Build in-memory collection for return value
                if ($event instanceof MetricRecorded) {
                    $metrics->put($metricUuid, [
                        'uuid' => $metricUuid,
                        'profile_uuid' => $event->profileUuid,
                        'type' => MetricType::from($event->type),
                        'value' => $event->value,
                        'notes' => $event->notes,
                        'photo_url' => $event->photoUrl,
                        'timestamp' => $event->timestamp,
                        'deleted' => false,
                        'deleted_at' => null,
                    ]);
                } elseif ($event instanceof MetricDeleted) {
                    if ($metrics->has($metricUuid)) {
                        $metric = $metrics->get($metricUuid);
                        $metric['deleted'] = true;
                        $metric['deleted_at'] = $storedEvent->timestamp;
                        $metrics->put($metricUuid, $metric);
                    }
                }
            }
        });

        if (!$this->includeDeleted) {
            $metrics = $metrics->reject(fn($metric) => $metric['deleted']);
        }

        return $metrics->values();
    }
}
