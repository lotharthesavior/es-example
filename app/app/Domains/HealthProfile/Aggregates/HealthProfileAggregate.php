<?php

namespace App\Domains\HealthProfile\Aggregates;

use App\Domains\HealthProfile\Events\MetricDeleted;
use App\Domains\HealthProfile\Events\MetricRecorded;
use App\Enums\MetricType;
use Illuminate\Support\Carbon;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class HealthProfileAggregate extends AggregateRoot
{
    public ?string $type = null;
    public ?array $value = null;
    public ?string $profileUuid = null;
    public ?string $notes = null;
    public ?string $photoUrl = null;
    public ?Carbon $timestamp = null;
    public bool $deleted = false;

    public function recordMetric(
        MetricType $type,
        array $value,
        string $profileUuid,
        ?string $notes = null,
        ?string $photoUrl = null,
        ?Carbon $timestamp = null,
    ) {
        $this->recordThat(new MetricRecorded(
            type: $type->value,
            value: $value,
            profileUuid: $profileUuid,
            notes: $notes,
            photoUrl: $photoUrl,
            timestamp: $timestamp ?? now(),
        ));

        return $this;
    }

    public function deleteMetric(): self
    {
        $this->recordThat(new MetricDeleted());
        return $this;
    }

    protected function applyMetricRecorded(MetricRecorded $event): void
    {
        $this->type = $event->type;
        $this->value = $event->value;
        $this->profileUuid = $event->profileUuid;
        $this->notes = $event->notes;
        $this->photoUrl = $event->photoUrl;
        $this->timestamp = $event->timestamp instanceof Carbon
            ? $event->timestamp
            : Carbon::parse($event->timestamp);
    }

    protected function applyMetricDeleted(MetricDeleted $event): void
    {
        $this->deleted = true;
    }

    protected function useState(array $state): void
    {
        $this->type = $state['type'] ?? null;
        $this->value = $state['value'] ?? null;
        $this->profileUuid = $state['profileUuid'] ?? null;
        $this->notes = $state['notes'] ?? null;
        $this->photoUrl = $state['photoUrl'] ?? null;
        $this->timestamp = isset($state['timestamp']) && $state['timestamp'] !== null
            ? Carbon::parse($state['timestamp'])
            : null;
        $this->deleted = $state['deleted'] ?? false;
    }
}
