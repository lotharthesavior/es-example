<?php

namespace App\Domains\Profile\CommandHandlers;

use App\Domains\Profile\Aggregates\ProfileAggregate;
use App\Domains\Profile\Commands\DeleteProfileCommand;
use App\EventSourcing\Interfaces\CommandHandlerInterface;
use App\EventSourcing\Interfaces\CommandInterface;
use InvalidArgumentException;

class DeleteProfileCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof DeleteProfileCommand) {
            throw new InvalidArgumentException('Expected CreateProfileCommand');
        }

        ProfileAggregate::retrieve($command->profileUuid)
            ->deleteProfile()
            ->persist();
    }
}
