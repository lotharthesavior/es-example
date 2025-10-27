<?php

namespace App\Domains\Profile\Events;

use Illuminate\Support\Carbon;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ProfileCreated extends ShouldBeStored
{
    public function __construct(
        public string $name,
        public string $role,
        public string $instanceUuid,
        public ?int $userId = null,
        public ?Carbon $timestamp = null,
    ) {}
}
