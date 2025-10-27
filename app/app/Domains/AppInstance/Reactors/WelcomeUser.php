<?php

namespace App\Domains\AppInstance\Reactors;

use App\Domains\AppInstance\Events\AppInstanceInitialized;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class WelcomeUser extends Reactor implements ShouldQueue
{
    public function onEventHappened(AppInstanceInitialized $event)
    {
        //
    }
}
