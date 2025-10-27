<?php

namespace App\Domains\HealthProfile\Reactors;

use App\Domains\HealthProfile\Events\MetricRecorded;
use App\Enums\MetricType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class DangerousHeartRate extends Reactor implements ShouldQueue
{
    public function onMetricRecorded(MetricRecorded $event)
    {
        if ($event->type !== MetricType::HEART_RATE->value) {
            return;
        }

        $bpm = (int) Arr::get($event->value, 'bpm');
        if ($bpm === null) {
            return;
        }

        if ($bpm >= 40 && $bpm <= 120) {
            return;
        }

        Log::info('DangerousHeartRate Reactor triggered for MetricRecorded event.', [
            'profileId' => $event->profileUuid,
            'metricType' => $event->type,
            'value' => $event->value,
        ]);
    }
}
