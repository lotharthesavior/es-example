<?php

namespace App\Domains\Profile\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ProfileSet extends ShouldBeStored
{
    public function __construct(
        public string $profileUuid,
    ) {}
}
