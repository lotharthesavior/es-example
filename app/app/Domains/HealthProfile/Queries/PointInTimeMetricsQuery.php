<?php

namespace App\Domains\HealthProfile\Queries;

use App\Domains\HealthProfile\Events\MetricDeleted;
use App\Domains\HealthProfile\Events\MetricRecorded;
use App\Enums\MetricType;
use App\Models\StoredEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Query to reproduce health metrics state at a specific point in time
 * by replaying events up until that moment.
 */
class PointInTimeMetricsQuery
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
     * (by default, deleted metrics are excluded)
     */
    public function includeDeleted(): self
    {
        $this->includeDeleted = true;
        return $this;
    }

    /**
     * Execute the query and return the reconstructed metrics state
     *
     * @return Collection<array{
     *     uuid: string,
     *     profile_uuid: string,
     *     type: MetricType,
     *     value: array,
     *     notes: ?string,
     *     photo_url: ?string,
     *     timestamp: ?Carbon,
     *     recorded_at: Carbon,
     *     deleted: bool,
     *     deleted_at: ?Carbon
     * }>
     */
    public function get(): Collection
    {
        $events = $this->queryStoredEvents();

        return $this->replayEvents($events);
    }

    /**
     * Get a single metric by UUID (returns null if not found or deleted)
     */
    public function first(): ?array
    {
        if (!$this->metricUuid) {
            throw new \InvalidArgumentException(
                'You must specify a metric UUID using forMetric() when using first()'
            );
        }

        $result = $this->get()->first();

        if (!$result || $result['deleted']) {
            return null;
        }

        return $result;
    }

    /**
     * Count the number of metrics at the point in time
     */
    public function count(): int
    {
        return $this->get()
            ->reject(fn($metric) => $metric['deleted'])
            ->count();
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

        // Filter by point in time
        if ($this->untilDateTime) {
            $query->where('created_at', '<=', $this->untilDateTime);
        }

        // Filter by profile UUID (stored in event_properties)
        if ($this->profileUuid) {
            $query->whereRaw(
                "JSON_EXTRACT(event_properties, '$.profileUuid') = ?",
                [$this->profileUuid]
            );
        }

        // Filter by metric type (stored in event_properties)
        if ($this->metricType) {
            $query->whereRaw(
                "JSON_EXTRACT(event_properties, '$.type') = ?",
                [$this->metricType->value]
            );
        }

        // Filter by specific metric UUID (aggregate_uuid)
        if ($this->metricUuid) {
            $query->where('aggregate_uuid', $this->metricUuid);
        }

        // Filter by multiple metric UUIDs
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
        $metrics = collect();

        foreach ($events as $storedEvent) {
            $event = $storedEvent->toStoredEvent()->event;
            $metricUuid = $storedEvent->aggregate_uuid;

            if ($event instanceof MetricRecorded) {
                $metrics->put($metricUuid, [
                    'uuid' => $metricUuid,
                    'profile_uuid' => $event->profileUuid,
                    'type' => MetricType::from($event->type),
                    'value' => $event->value,
                    'notes' => $event->notes,
                    'photo_url' => $event->photoUrl,
                    'timestamp' => $event->timestamp,
                    'recorded_at' => $storedEvent->created_at,
                    'deleted' => false,
                    'deleted_at' => null,
                ]);
            } elseif ($event instanceof MetricDeleted) {
                if ($metrics->has($metricUuid)) {
                    $metric = $metrics->get($metricUuid);
                    $metric['deleted'] = true;
                    $metric['deleted_at'] = $storedEvent->created_at;
                    $metrics->put($metricUuid, $metric);
                }
            }
        }

        // Filter out deleted metrics unless includeDeleted is true
        if (!$this->includeDeleted) {
            $metrics = $metrics->reject(fn($metric) => $metric['deleted']);
        }

        return $metrics->values();
    }

    /**
     * Get metrics grouped by type
     *
     * @return Collection<MetricType, Collection>
     */
    public function groupByType(): Collection
    {
        return $this->get()
            ->groupBy(fn($metric) => $metric['type']->value)
            ->mapWithKeys(fn($group, $type) => [
                MetricType::from($type) => $group
            ]);
    }

    /**
     * Get the latest value for each metric type for a profile
     * Useful for getting the current state of all health metrics
     *
     * @return Collection<MetricType, array>
     */
    public function latestByType(): Collection
    {
        if (!$this->profileUuid) {
            throw new \InvalidArgumentException(
                'You must specify a profile UUID using forProfile() when using latestByType()'
            );
        }

        return $this->get()
            ->groupBy(fn($metric) => $metric['type']->value)
            ->map(fn($group) => $group->sortByDesc('timestamp')->first())
            ->mapWithKeys(fn($metric, $type) => [
                MetricType::from($type) => $metric
            ]);
    }

    /**
     * Get metrics within a time range
     * Note: This uses the metric's timestamp field, not when it was recorded
     */
    public function betweenTimestamps(Carbon|string $start, Carbon|string $end): Collection
    {
        $startDate = $start instanceof Carbon ? $start : Carbon::parse($start);
        $endDate = $end instanceof Carbon ? $end : Carbon::parse($end);

        return $this->get()
            ->filter(fn($metric) =>
                $metric['timestamp'] !== null
                && $metric['timestamp']->between($startDate, $endDate)
            );
    }

    /**
     * Get average value for numeric metrics
     * Works for simple numeric values like heart rate, blood sugar, weight, etc.
     */
    public function average(string $valueKey = null): ?float
    {
        $metrics = $this->get()->reject(fn($metric) => $metric['deleted']);

        if ($metrics->isEmpty()) {
            return null;
        }

        // If no value key specified, try to determine it from the metric type
        if ($valueKey === null) {
            $firstMetric = $metrics->first();
            if (!$firstMetric) {
                return null;
            }

            $valueKey = $this->determineValueKey($firstMetric['type']);
        }

        $values = $metrics
            ->pluck('value')
            ->map(fn($value) => $value[$valueKey] ?? null)
            ->filter(fn($value) => $value !== null && is_numeric($value));

        if ($values->isEmpty()) {
            return null;
        }

        return $values->average();
    }

    /**
     * Determine the appropriate value key for averaging based on metric type
     */
    protected function determineValueKey(MetricType $type): string
    {
        return match($type) {
            MetricType::HEART_RATE => 'bpm',
            MetricType::BLOOD_SUGAR => 'level',
            MetricType::SPO2 => 'spo2',
            MetricType::WEIGHT => 'weight',
            MetricType::TEMPERATURE => 'temperature',
            default => throw new \InvalidArgumentException(
                "Cannot automatically determine value key for {$type->value}. Please specify the key explicitly."
            ),
        };
    }
}
