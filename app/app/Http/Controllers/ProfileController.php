<?php

namespace App\Http\Controllers;

use App\Domains\AppInstance\CommandHandlers\SetProfileCommandHandler;
use App\Domains\Profile\CommandHandlers\CreateProfileCommandHandler;
use App\Domains\Profile\CommandHandlers\DeleteProfileCommandHandler;
use App\Domains\Profile\CommandHandlers\UpdateProfileCommandHandler;
use App\Domains\Profile\Commands\DeleteProfileCommand;
use App\Domains\Profile\Projections\Profile;
use App\Http\Requests\SetProfileRequest;
use App\Http\Requests\StoreProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $profiles = Profile::all();

        return view('profiles.index', [
            'profiles' => $profiles,
        ]);
    }

    public function create(): View
    {
        return view('profiles.create');
    }

    public function edit(Profile $profile): View
    {
        return view('profiles.edit', [
            'profile' => $profile,
        ]);
    }

    public function destroy(
        Profile $profile,
        DeleteProfileCommandHandler $commandHandler
    ): RedirectResponse {
        $commandHandler->handle(new DeleteProfileCommand($profile->uuid));

        return redirect()->route('profiles.index')->with('success', 'Profile deleted successfully');
    }

    public function store(
        StoreProfileRequest $request,
        CreateProfileCommandHandler $createCommandHandler,
        UpdateProfileCommandHandler $updateCommandHandler,
    ): RedirectResponse {
        $command = $request->getCommand();

        if ($request->profile === null) {
            $createCommandHandler->handle($command);
            $message = 'Profile created successfully';
        } else {
            $updateCommandHandler->handle($command);
            $message = 'Profile updated successfully';
        }

        return redirect()
            ->route('profiles.index')
            ->with('success', $message);
    }

    public function setProfile(
        SetProfileRequest $request,
        SetProfileCommandHandler $commandHandler
    ): RedirectResponse {
        $commandHandler->handle($request->getCommand());

        return redirect()
            ->back()
            ->with('success', 'Profile selected successfully');
    }
}
