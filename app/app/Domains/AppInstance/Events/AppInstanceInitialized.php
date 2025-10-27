<?php

namespace App\Domains\AppInstance\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AppInstanceInitialized extends ShouldBeStored
{
    public function __construct(
        public ?int $userId = null,
        public ?string $profileUuid = null,
    ) {}
}
