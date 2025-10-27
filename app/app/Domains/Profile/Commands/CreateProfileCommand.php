<?php

namespace App\Domains\Profile\Commands;

use App\EventSourcing\Interfaces\CommandInterface;

class CreateProfileCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $role,
        public string $instanceUuid,
        public ?int $userId,
    ) {}
}
