<?php

namespace App\Domains\AppInstance\CommandHandlers;

use App\Domains\AppInstance\Aggregates\AppInstanceAggregate;
use App\Domains\AppInstance\Commands\SetProfileCommand;
use App\EventSourcing\Interfaces\CommandHandlerInterface;
use App\EventSourcing\Interfaces\CommandInterface;
use InvalidArgumentException;

class SetProfileCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof SetProfileCommand) {
            throw new InvalidArgumentException('Expected SetProfileCommand');
        }

        AppInstanceAggregate::retrieve($command->appInstanceUuid)
            ->setProfile(
                profileUuid: $command->profileUuid,
            )
            ->persist();
    }
}
