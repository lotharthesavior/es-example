<?php

namespace App\Domains\HealthProfile\CommandHandlers;

use App\Domains\HealthProfile\Aggregates\HealthProfileAggregate;
use App\Domains\HealthProfile\Commands\DeleteHealthMetricCommand;
use App\EventSourcing\Interfaces\CommandInterface;
use InvalidArgumentException;

class DeleteHealthMetricCommandHandler
{
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof DeleteHealthMetricCommand) {
            throw new InvalidArgumentException('Expected DeleteHealthMetricCommand');
        }

        HealthProfileAggregate::retrieve($command->healthMetricUuid)
            ->deleteMetric()
            ->persist();
    }
}
