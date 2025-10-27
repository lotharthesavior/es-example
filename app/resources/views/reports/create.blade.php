<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Generate Health Report') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">{{ __('Generate Health Report') }}</h1>

    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-800 text-green-700 dark:text-green-200 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('health.reports') }}" method="GET" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md max-w-lg">
        <!-- Start Date -->
        <div class="mb-4">
            <label for="start_date" class="block text-sm font-medium">{{ __('Start Date') }}</label>
            <input type="date" id="start_date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required>
            @error('start_date')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- End Date -->
        <div class="mb-4">
            <label for="end_date" class="block text-sm font-medium">{{ __('End Date') }}</label>
            <input type="date" id="end_date" name="end_date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100" required>
            @error('end_date')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Metric Types -->
        <div class="mb-4">
            <label class="block text-sm font-medium">{{ __('Metric Types (Select all that apply)') }}</label>
            <div class="mt-2 space-y-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="metric_types[]" value="blood_pressure" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500">
                    <span class="ml-2">{{ __('Blood Pressure') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="metric_types[]" value="heart_rate" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500">
                    <span class="ml-2">{{ __('Heart Rate') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="metric_types[]" value="blood_sugar" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500">
                    <span class="ml-2">{{ __('Blood Sugar') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="metric_types[]" value="spo2" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500">
                    <span class="ml-2">{{ __('SpO2') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="metric_types[]" value="breastfeeding" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500">
                    <span class="ml-2">{{ __('Breastfeeding') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="metric_types[]" value="weight" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500">
                    <span class="ml-2">{{ __('Weight') }}</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="metric_types[]" value="temperature" class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-blue-500">
                    <span class="ml-2">{{ __('Temperature') }}</span>
                </label>
            </div>
            @error('metric_types')
            <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">{{ __('Generate Report') }}</button>
        </div>
    </form>
</div>
</body>
</html>
