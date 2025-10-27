<?php

namespace App\Domains\HealthProfile\Events;

use Illuminate\Support\Carbon;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MetricRecorded extends ShouldBeStored
{
    public function __construct(
        public string $type,
        public array $value,  // e.g., ['systolic' => 120, 'diastolic' => 80] or ['bpm' => 75]
        public string $profileUuid,
        public ?string $notes = null,
        public ?string $photoUrl = null,
        public ?Carbon $timestamp = null,
    ) {}
}
