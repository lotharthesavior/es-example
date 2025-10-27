<?php

namespace App\Domains\HealthProfile\Projectors;

use App\Domains\HealthProfile\Events\MetricDeleted;
use App\Domains\HealthProfile\Events\MetricRecorded;
use App\Domains\HealthProfile\Projections\Metric;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\EventHandlers\Projectors\ProjectsEvents;

class HealthMetricsProjector extends Projector
{
    use ProjectsEvents;

    public function onMetricRecorded(MetricRecorded $event)
    {
        $metric = Metric::findByUuid($event->aggregateRootUuid());
        if ($metric === null) {
            $metric = new Metric([
                'uuid' => $event->aggregateRootUuid(),
            ]);
        }

        $metric->profile_uuid = $event->profileUuid;
        $metric->type = $event->type;
        $metric->value = $event->value;
        $metric->notes = $event->notes;
        $metric->photo_url = $event->photoUrl;
        $metric->timestamp = $event->timestamp ?? now();
        $metric->writeable()->save();
    }

    public function onMetricDeleted(MetricDeleted $event)
    {
        $metric = Metric::findByUuid($event->aggregateRootUuid());
        if ($metric) {
            $metric->writeable()->delete();
        }
    }

    public function onStartingEventReplay()
    {
        Metric::truncate();
    }
}
