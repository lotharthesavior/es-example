<?php

namespace App\Domains\Profile\Aggregates;

use App\Domains\Profile\Events\ProfileCreated;
use App\Domains\Profile\Events\ProfileDeleted;
use App\Domains\Profile\Events\ProfileUpdated;
use Illuminate\Support\Carbon;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ProfileAggregate extends AggregateRoot
{
    public ?string $name = null;
    public ?string $role = null;
    public ?string $instanceUuid = null;
    public ?int $userId = null;
    public ?Carbon $createdAt = null;
    public ?Carbon $updatedAt = null;
    public ?Carbon $deletedAt = null;
    public bool $deleted = false;

    public function createProfile(
        string $name,
        string $role,
        string $instanceUuid,
        ?int $userId = null
    ): self {
        $this->recordThat(new ProfileCreated(
            name: $name,
            role: $role,
            instanceUuid: $instanceUuid,
            userId: $userId,
            timestamp: now(),
        ));

        return $this;
    }

    public function updateProfile(
        string $name,
        string $role,
    ): self {
        $this->recordThat(new ProfileUpdated(
            name: $name,
            role: $role,
            timestamp: now(),
        ));

        return $this;
    }

    public function deleteProfile(): self
    {
        $this->recordThat(new ProfileDeleted(
            timestamp: now(),
        ));
        return $this;
    }

    protected function applyProfileCreated(ProfileCreated $event): void
    {
        $this->name = $event->name;
        $this->role = $event->role;
        $this->instanceUuid = $event->instanceUuid;
        $this->userId = $event->userId;
        $this->createdAt = $event->timestamp instanceof Carbon
            ? $event->timestamp
            : Carbon::parse($event->timestamp);
    }

    protected function applyProfileUpdated(ProfileUpdated $event): void
    {
        $this->name = $event->name;
        $this->role = $event->role;
        $this->updatedAt = $event->timestamp instanceof Carbon
            ? $event->timestamp
            : Carbon::parse($event->timestamp);
    }

    protected function applyProfileDeleted(ProfileDeleted $event): void
    {
        $this->deleted = true;
        $this->deletedAt = $event->timestamp instanceof Carbon
            ? $event->timestamp
            : Carbon::parse($event->timestamp);
    }

    protected function getState(): array
    {
        return [
            'name' => $this->name,
            'role' => $this->role,
            'instanceUuid' => $this->instanceUuid,
            'userId' => $this->userId,
            'createdAt' => $this->createdAt?->toIso8601String(),
            'updatedAt' => $this->updatedAt?->toIso8601String(),
            'deletedAt' => $this->deletedAt?->toIso8601String(),
            'deleted' => $this->deleted,
        ];
    }

    protected function useState(array $state): void
    {
        $this->name = $state['name'] ?? null;
        $this->role = $state['role'] ?? null;
        $this->instanceUuid = $state['instanceUuid'] ?? null;
        $this->userId = $state['userId'] ?? null;
        $this->createdAt = isset($state['createdAt']) && $state['createdAt'] !== null
            ? Carbon::parse($state['createdAt'])
            : null;
        $this->updatedAt = isset($state['updatedAt']) && $state['updatedAt'] !== null
            ? Carbon::parse($state['updatedAt'])
            : null;
        $this->deletedAt = isset($state['deletedAt']) && $state['deletedAt'] !== null
            ? Carbon::parse($state['deletedAt'])
            : null;
        $this->deleted = $state['deleted'] ?? false;
    }
}
