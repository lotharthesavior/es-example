<?php

namespace App\Domains\HealthProfile\Commands;

use App\Enums\MetricType;
use App\EventSourcing\Interfaces\CommandInterface;
use Illuminate\Support\Carbon;

class StoreHealthMetricCommand implements CommandInterface
{
    /**
     * @param string $metricUuid
     * @param string $profileUuid
     * @param MetricType $type
     * @param array<array-key, mixed> $value
     * @param string|null $notes
     * @param string|null $photoUrl
     * @param Carbon|null $timestamp
     */
    public function __construct(
        public string $metricUuid,
        public string $profileUuid,
        public MetricType $type,
        public array $value,
        public ?string $notes = null,
        public ?string $photoUrl = null,
        public ?Carbon $timestamp = null,
    ) {}
}
