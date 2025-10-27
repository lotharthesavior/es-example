<x-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">{{ __('Edit Profile') }}</h1>

        @include('parts.breadcrumb')

        @if (session('success'))
            <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profiles.store', $profile) }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md max-w-lg">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium">{{ __('Name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', $profile->name) }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required>
                @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium">{{ __('Role') }}</label>
                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required>
                    <option value="patient" {{ old('role', $profile->role) == 'patient' ? 'selected' : '' }}>{{ __('Patient') }}</option>
                    <option value="caregiver" {{ old('role', $profile->role) == 'caregiver' ? 'selected' : '' }}>{{ __('Caregiver') }}</option>
                </select>
                @error('role')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">{{ __('Update Profile') }}</button>
            </div>
        </form>
    </div>
</x-layout>
