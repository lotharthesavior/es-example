<?php

namespace App\Domains\Profile\Events;

use Illuminate\Support\Carbon;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ProfileUpdated extends ShouldBeStored
{
    public function __construct(
        public string $name,
        public string $role,
        public ?Carbon $timestamp = null,
    ) {}
}
