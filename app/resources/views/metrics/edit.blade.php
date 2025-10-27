@use('App\Enums\MetricType')
@use('App\Helpers\Helpers')

<x-layout>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">{{ __('Edit Health Metric') }}</h1>

        @include('parts.breadcrumb')

        <form action="{{ route('health.metrics.update', $metric->uuid) }}" method="POST" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md max-w-lg">
            @csrf
            @method('PUT')

            <h2 class="text-xl font-semibold mb-4">{{ Helpers::prettyString($metric->type->value) }}</h2>
            <input type="hidden" name="type" id="type" value="{{ $metric->type }}" />

            @if ($errors->any())
                <div class="mb-4">
                    <ul class="text-red-500 text-sm list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Value Fields (Dynamic based on type) -->
            <div id="value-fields" class="mb-4">
                <!-- Blood Pressure -->
                <div class="value-group blood_pressure hidden">
                    <label for="systolic" class="block text-sm font-medium">{{ __('Systolic (mmHg)') }}</label>
                    <input type="number" id="systolic" name="value[systolic]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" value="{{ old('value.systolic', $metric->value['systolic'] ?? '') }}">
                    @error('value.systolic')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <label for="diastolic" class="block text-sm font-medium mt-2">{{ __('Diastolic (mmHg)') }}</label>
                    <input type="number" id="diastolic" name="value[diastolic]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" value="{{ old('value.diastolic', $metric->value['diastolic'] ?? '') }}">
                    @error('value.diastolic')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Heart Rate -->
                <div class="value-group heart_rate hidden">
                    <label for="bpm" class="block text-sm font-medium">{{ __('Heart Rate (BPM)') }}</label>
                    <input type="number" id="bpm" name="value[bpm]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" value="{{ old('value.bpm', $metric->value['bpm'] ?? '') }}">
                    @error('value.bpm')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Blood Sugar -->
                <div class="value-group blood_sugar hidden">
                    <label for="level" class="block text-sm font-medium">{{ __('Blood Sugar (mg/dL)') }}</label>
                    <input type="number" id="level" name="value[level]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" step="0.1" value="{{ old('value.level', $metric->value['level'] ?? '') }}">
                    @error('value.level')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- SpO2 -->
                <div class="value-group spo2 hidden">
                    <label for="spo2" class="block text-sm font-medium">{{ __('SpO2 (%)') }}</label>
                    <input type="number" id="spo2" name="value[spo2]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" max="100" value="{{ old('value.spo2', $metric->value['spo2'] ?? '') }}">
                    @error('value.spo2')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Breastfeeding -->
                <div class="value-group breastfeeding hidden">
                    <label for="duration" class="block text-sm font-medium">{{ __('Duration (minutes)') }}</label>
                    <input type="number" id="duration" name="value[duration]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" value="{{ old('value.duration', $metric->value['duration'] ?? '') }}">
                    @error('value.duration')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                    <label for="side" class="block text-sm font-medium mt-2">{{ __('Side') }}</label>
                    <select id="side" name="value[side]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                        <option value="left" {{ (old('value.side', $metric->value['side'] ?? '') == 'left') ? 'selected' : '' }}>{{ __('Left') }}</option>
                        <option value="right" {{ (old('value.side', $metric->value['side'] ?? '') == 'right') ? 'selected' : '' }}>{{ __('Right') }}</option>
                        <option value="both" {{ (old('value.side', $metric->value['side'] ?? '') == 'both') ? 'selected' : '' }}>{{ __('Both') }}</option>
                    </select>
                    @error('value.side')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Weight -->
                <div class="value-group weight hidden">
                    <label for="weight" class="block text-sm font-medium">{{ __('Weight (kg)') }}</label>
                    <input type="number" id="weight" name="value[weight]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" step="0.1" value="{{ old('value.weight', $metric->value['weight'] ?? '') }}">
                    @error('value.weight')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Temperature -->
                <div class="value-group temperature hidden">
                    <label for="temperature" class="block text-sm font-medium">{{ __('Temperature (°C)') }}</label>
                    <input type="number" id="temperature" name="value[temperature]" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" min="0" step="0.1" value="{{ old('value.temperature', $metric->value['temperature'] ?? '') }}">
                    @error('value.temperature')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mb-4">
                <label for="notes" class="block text-sm font-medium">{{ __('Notes') }}</label>
                <textarea id="notes" name="notes" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" rows="4">{{ old('notes', $metric->notes) }}</textarea>
                @error('notes')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Photo URL -->
            <div class="mb-4">
                <label for="photo_url" class="block text-sm font-medium">{{ __('Photo URL') }}</label>
                <input type="url" id="photo_url" name="photo_url" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" value="{{ old('photo_url', $metric->photo_url) }}">
                @error('photo_url')
                <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">{{ __('Update Metric') }}</button>
            </div>
        </form>
    </div>

    <script>
        function updateFormByType() {
            const typeSelect = document.getElementById('type');
            const selectedType = typeSelect.value;
            const valueGroups = document.querySelectorAll('.value-group');
            valueGroups.forEach(group => group.classList.add('hidden'));
            const selectedGroup = document.querySelector(`.value-group.${selectedType}`);
            if (selectedGroup) {
                selectedGroup.classList.remove('hidden');
            }

            // disabling other values:
            valueGroups.forEach(group => {
                group.querySelectorAll('input, select').forEach(input => {
                    input.disabled = true;
                });
            });
            if (selectedGroup) {
                selectedGroup.querySelectorAll('input, select').forEach(input => {
                    input.disabled = false;
                });
            }
        }

        document.getElementById('type').addEventListener('change', updateFormByType);
        updateFormByType();
    </script>
</x-layout>
