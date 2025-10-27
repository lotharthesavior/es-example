<?php

namespace App\Domains\Profile\Projectors;

use App\Domains\Profile\Events\ProfileCreated;
use App\Domains\Profile\Events\ProfileDeleted;
use App\Domains\Profile\Events\ProfileUpdated;
use App\Domains\Profile\Projections\Profile;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;
use Spatie\EventSourcing\EventHandlers\Projectors\ProjectsEvents;

class ProfileProjector extends Projector
{
    use ProjectsEvents;

    public function onProfileCreated(ProfileCreated $event)
    {
        (new Profile([
            'uuid' => $event->aggregateRootUuid(),
            'name' => $event->name,
            'role' => $event->role,
            'instance_uuid' => $event->instanceUuid,
            'user_id' => $event->userId,
            'created_at' => $event->timestamp ?? now(),
        ]))->writeable()->save();
    }

    public function onProfileUpdated(ProfileUpdated $event)
    {
        $profile = Profile::findByUuid($event->aggregateRootUuid());
        if ($profile === null) {
            return;
        }

        $profile->name = $event->name;
        $profile->role = $event->role;
        $profile->updated_at = $event->timestamp ?? now();
        $profile->writeable()->save();
    }

    public function onProfileDeleted(ProfileDeleted $event)
    {
        $profile = Profile::findByUuid($event->aggregateRootUuid());
        if ($profile === null) {
            return;
        }

        $profile->deleted_at = $event->timestamp ?? now();
        $profile->writeable()->save();
    }

    public function onStartingEventReplay()
    {
        Profile::truncate();
    }
}
