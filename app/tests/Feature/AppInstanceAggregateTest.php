<?php

namespace Tests\Feature;

use App\Domains\AppInstance\Aggregates\AppInstanceAggregate;
use App\Domains\AppInstance\Events\AppInstanceInitialized;
use App\Domains\AppInstance\Projections\AppInstance;
use App\Domains\Profile\Events\ProfileSet;
use App\Models\User;
use Illuminate\Support\Str;
use function PHPUnit\Framework\assertEquals;

test('AppInstanceAggregate initializes app and sets profile', function (
    ?int $userId,
    string $profileUuid,
) {
    AppInstanceAggregate::fake()
        ->given([
            // No prior events
        ])
        ->when(function (AppInstanceAggregate $aggregate) use ($userId, $profileUuid) {
            $aggregate->initializeApp(userId: $userId);
            $aggregate->setProfile(profileUuid: $profileUuid);
        })
        ->assertRecorded([
            new AppInstanceInitialized(
                userId: $userId,
                profileUuid: null,
            ),
            new ProfileSet(
                profileUuid: $profileUuid,
            ),
        ]);
})->with([
    'simple profile' => [
        'userId' => null,
        'profileUuid' => 'profile-uuid-123',
    ],
    'with user id' => [
        'userId' => 1,
        'profileUuid' => 'another-profile-uuid-456',
    ],
]);

test('app instance projection persist app instance', function (
    bool $withUser,
    ?string $initialProfileUuid,
    ?string $secondaryProfileUuid,
) {
    $instanceUuid = Str::uuid()->toString();


    $user = null;
    if ($withUser) {
        $user = User::factory()->create();
    }

    AppInstanceAggregate::retrieve($instanceUuid)
        ->initializeApp(userId: $user?->id, profileUuid: $initialProfileUuid)
        ->persist();
    assertEquals(1, AppInstance::count());

    if ($secondaryProfileUuid === null) {
        return;
    }

    AppInstanceAggregate::retrieve($instanceUuid)
        ->setProfile(profileUuid: $secondaryProfileUuid)
        ->persist();
    assertEquals(1, AppInstance::count());
    assertEquals(AppInstance::first()->profile_uuid, $secondaryProfileUuid);
})->with([
    'simple profile' => [
        'withUser' => false,
        'initialProfileUuid' => null,
        'secondaryProfileUuid' => 'profile-uuid-123',
    ],
    'with user id' => [
        'withUser' => true,
        'initialProfileUuid' => 'another-profile-uuid-456',
        'secondaryProfileUuid' => null,
    ],
]);
