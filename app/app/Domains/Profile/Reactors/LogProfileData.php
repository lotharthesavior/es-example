<?php

namespace App\Domains\Profile\Reactors;

use App\Domains\Profile\Events\ProfileCreated;
use App\Domains\Profile\Events\ProfileUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class LogProfileData extends Reactor implements ShouldQueue
{
    public function onEventHappened(ProfileCreated|ProfileUpdated $event)
    {
        Log::info('Profile event handled', [
            'event' => get_class($event),
            'data' => json_encode($event),
        ]);
    }
}
