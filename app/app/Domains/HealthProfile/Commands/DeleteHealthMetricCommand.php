<?php

namespace App\Domains\HealthProfile\Commands;

use App\EventSourcing\Interfaces\CommandInterface;

class DeleteHealthMetricCommand implements CommandInterface
{
    public function __construct(
        public readonly string $healthMetricUuid,
    ) {}
}
