<?php

namespace App\Domains\Profile\Commands;

use App\EventSourcing\Interfaces\CommandInterface;

class DeleteProfileCommand implements CommandInterface
{
    public function __construct(
        public string $profileUuid, // aggregate uuid
    ) {}
}
