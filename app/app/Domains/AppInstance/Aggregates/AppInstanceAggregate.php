<?php

namespace App\Domains\AppInstance\Aggregates;

use App\Domains\AppInstance\Events\AppInstanceInitialized;
use App\Domains\Profile\Events\ProfileSet;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AppInstanceAggregate extends AggregateRoot
{
    public ?int $userId = null;
    public ?string $profileUuid = null;

    public function initializeApp(
        ?int $userId = null,
        ?string $profileUuid = null,
    ) {
        $this->recordThat(new AppInstanceInitialized(
            userId: $userId,
            profileUuid: $profileUuid,
        ));

        return $this;
    }

    public function setProfile(string $profileUuid)
    {
        $this->recordThat(new ProfileSet(
            profileUuid: $profileUuid,
        ));

        return $this;
    }

    protected function applyAppInstanceInitialized(AppInstanceInitialized $event): void
    {
        $this->userId = $event->userId !== null ? (int) $event->userId : null;
        $this->profileUuid = $event->profileUuid;
    }

    protected function applyProfileSet(ProfileSet $event): void
    {
        $this->profileUuid = $event->profileUuid;
    }
}
