<?php

namespace App\Domains\Profile\Projectors;

use App\Domains\HealthProfile\Events\MetricRecorded;
use App\Domains\Profile\Projections\Profile;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\EventHandlers\Projectors\ProjectsEvents;

class ProfileHealthMetricProjector extends Projector
{
    use ProjectsEvents;

    public function onMetricRecorded(MetricRecorded $event)
    {
        $profile = Profile::findByUuid($event->profileUuid);
        $profile->last_metric_type = $event->type;
        $profile->last_metric_at = $event->timestamp ?? now();
        $profile->writeable()->save();
    }
}
