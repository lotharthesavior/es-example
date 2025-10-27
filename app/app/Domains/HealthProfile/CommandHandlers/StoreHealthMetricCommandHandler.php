<?php

namespace App\Domains\HealthProfile\CommandHandlers;

use App\Domains\HealthProfile\Aggregates\HealthProfileAggregate;
use App\Domains\HealthProfile\Commands\StoreHealthMetricCommand;
use App\EventSourcing\Interfaces\CommandHandlerInterface;
use App\EventSourcing\Interfaces\CommandInterface;
use InvalidArgumentException;

class StoreHealthMetricCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof StoreHealthMetricCommand) {
            throw new InvalidArgumentException('Expected StoreHealthMetricCommand');
        }

        HealthProfileAggregate::retrieve($command->metricUuid)
            ->recordMetric(
                type: $command->type,
                value: $command->value,
                profileUuid: $command->profileUuid,
                notes: $command->notes,
                photoUrl: $command->photoUrl,
                timestamp: $command->timestamp,
            )
            ->persist();
    }
}
