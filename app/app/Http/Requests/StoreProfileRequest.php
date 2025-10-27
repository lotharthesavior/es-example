<?php

namespace App\Http\Requests;

use App\Domains\Profile\Commands\CreateProfileCommand;
use App\Domains\Profile\Commands\UpdateProfileCommand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Context;

class StoreProfileRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'role' => 'required|in:patient,caregiver',
        ];
    }

    public function getCommand(): CreateProfileCommand|UpdateProfileCommand
    {
        $validated = $this->validated();

        if ($this->profile === null) {
            return new CreateProfileCommand(
                name: Arr::get($validated, 'name'),
                role: Arr::get($validated, 'role'),
                instanceUuid: Context::get('instance'),
                userId: auth()->id(),
            );
        }

        return new UpdateProfileCommand(
            profileUuid: $this->profile,
            name: Arr::get($validated, 'name'),
            role: Arr::get($validated, 'role'),
            instanceUuid: Context::get('instance'),
            userId: auth()->id(),
        );
    }
}
