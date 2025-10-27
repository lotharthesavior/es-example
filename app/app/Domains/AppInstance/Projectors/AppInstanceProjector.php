<?php

namespace App\Domains\AppInstance\Projectors;

use App\Domains\AppInstance\Events\AppInstanceInitialized;
use App\Domains\AppInstance\Projections\AppInstance;
use App\Domains\Profile\Events\ProfileSet;
use App\Models\Traits\HasUuid;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\EventHandlers\Projectors\ProjectsEvents;

class AppInstanceProjector extends Projector
{
    use HasUuid;
    use ProjectsEvents;

    public function onAppInstanceInitialized(AppInstanceInitialized $event)
    {
        (new AppInstance([
            'uuid' => $event->aggregateRootUuid(),
            'user_id' => $event->userId,
            'profile_uuid' => $event->profileUuid,
            'timestamp' => now(),
        ]))->writeable()->save();
    }

    public function onProfileSet(ProfileSet $event)
    {
        $appInstance = AppInstance::where(['uuid' => $event->aggregateRootUuid()])->first();

        if ($appInstance === null) {
            Log::error('AppInstance not found for id: '.$event->aggregateRootUuid());

            return;
        }

        $appInstance->profile_uuid = $event->profileUuid;
        $appInstance->writeable()->save();
    }

    public function onStartingEventReplay()
    {
        AppInstance::truncate();
    }
}
