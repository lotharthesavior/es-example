<?php

namespace App\EventSourcing\Interfaces;

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): void;
}
