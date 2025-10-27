<?php

namespace App\Domains\Profile\Events;

use Illuminate\Support\Carbon;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ProfileDeleted extends ShouldBeStored
{
    public function __construct(
        public ?Carbon $timestamp = null,
    ) {}
}
