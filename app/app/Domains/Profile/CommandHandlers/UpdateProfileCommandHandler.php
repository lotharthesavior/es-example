<?php

namespace App\Domains\Profile\CommandHandlers;

use App\Domains\Profile\Aggregates\ProfileAggregate;
use App\Domains\Profile\Commands\UpdateProfileCommand;
use App\EventSourcing\Interfaces\CommandHandlerInterface;
use App\EventSourcing\Interfaces\CommandInterface;
use InvalidArgumentException;

class UpdateProfileCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof UpdateProfileCommand) {
            throw new InvalidArgumentException('Expected UpdateProfileCommand');
        }

        ProfileAggregate::retrieve($command->profileUuid)
            ->updateProfile(
                name: $command->name,
                role: $command->role,
            )
            ->persist();
    }
}
