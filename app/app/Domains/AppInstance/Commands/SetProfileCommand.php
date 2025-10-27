<?php

namespace App\Domains\AppInstance\Commands;

use App\EventSourcing\Interfaces\CommandInterface;

class SetProfileCommand implements CommandInterface
{
    public function __construct(
        public string $appInstanceUuid,
        public string $profileUuid,
    ) {}
}
