<?php

namespace App\Domains\Profile\CommandHandlers;

use App\Domains\Profile\Aggregates\ProfileAggregate;
use App\Domains\Profile\Commands\CreateProfileCommand;
use App\EventSourcing\Interfaces\CommandHandlerInterface;
use App\EventSourcing\Interfaces\CommandInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CreateProfileCommandHandler implements CommandHandlerInterface
{
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof CreateProfileCommand) {
            throw new InvalidArgumentException('Expected CreateProfileCommand');
        }

        $profileUuid = Str::uuid()->toString();

        ProfileAggregate::retrieve($profileUuid)
            ->createProfile(
                name: $command->name,
                role: $command->role,
                instanceUuid: $command->instanceUuid,
                userId: $command->userId,
            )->persist();
    }
}
