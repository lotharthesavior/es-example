<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Health Metrics Report') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 min-h-screen">
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">{{ __('Health Metrics Report') }}</h1>
    <p class="mb-4">{{ __('Date Range') }}: {{ $start_date }} {{ __('to') }} {{ $end_date }}</p>

    <!-- Averages Summary -->
    @if ($averages->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2">{{ __('Summary Statistics') }}</h2>
            <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg">
                <thead>
                <tr class="bg-gray-200 dark:bg-gray-700">
                    <th class="py-2 px-4 text-left">{{ __('Metric Type') }}</th>
                    <th class="py-2 px-4 text-left">{{ __('Average') }}</th>
                    <th class="py-2 px-4 text-left">{{ __('Min') }}</th>
                    <th class="py-2 px-4 text-left">{{ __('Max') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($averages as $type => $stats)
                    <tr class="border-b dark:border-gray-600">
                        <td class="py-2 px-4">{{ __($type) }}</td>
                        <td class="py-2 px-4">{{ number_format($stats['average'], 2) }}</td>
                        <td class="py-2 px-4">{{ number_format($stats['min'], 2) }}</td>
                        <td class="py-2 px-4">{{ number_format($stats['max'], 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Metrics Table -->
    <div class="overflow-x-auto mb-8">
        <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg">
            <thead>
            <tr class="bg-gray-200 dark:bg-gray-700">
                <th class="py-2 px-4 text-left">{{ __('Type') }}</th>
                <th class="py-2 px-4 text-left">{{ __('Value') }}</th>
                <th class="py-2 px-4 text-left">{{ __('Timestamp') }}</th>
                <th class="py-2 px-4 text-left">{{ __('Notes') }}</th>
                <th class="py-2 px-4 text-left">{{ __('Photo') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($metrics as $metric)
                <tr class="border-b dark:border-gray-600">
                    <td class="py-2 px-4">{{ __($metric->type) }}</td>
                    <td class="py-2 px-4">{{ json_encode($metric->value) }}</td>
                    <td class="py-2 px-4">{{ $metric->timestamp->format('Y-m-d H:i:s') }}</td>
                    <td class="py-2 px-4">{{ $metric->notes ?? '-' }}</td>
                    <td class="py-2 px-4">
                        @if ($metric->photo_url)
                            <a href="{{ $metric->photo_url }}" target="_blank" class="text-blue-500 hover:underline">{{ __('View') }}</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Chart -->
    @if ($metrics->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4">{{ __('Metrics Trend') }}</h2>
            <canvas id="metricsChart" class="w-full h-96"></canvas>
        </div>
    @endif

    <!-- Export Buttons -->
    <div class="flex space-x-4">
        <form action="{{ route('health.reports.pdf') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" value="{{ $start_date }}">
            <input type="hidden" name="end_date" value="{{ $end_date }}">
            @foreach ($metric_types as $type)
                <input type="hidden" name="metric_types[]" value="{{ $type }}">
            @endforeach
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">{{ __('Export to PDF') }}</button>
        </form>
        <form action="{{ route('health.reports.csv') }}" method="POST">
            @csrf
            <input type="hidden" name="start_date" value="{{ $start_date }}">
            <input type="hidden" name="end_date" value="{{ $end_date }}">
            @foreach ($metric_types as $type)
                <input type="hidden" name="metric_types[]" value="{{ $type }}">
            @endforeach
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">{{ __('Export to CSV') }}</button>
        </form>
    </div>
</div>

<script>
    // Chart.js configuration
    const ctx = document.getElementById('metricsChart')?.getContext('2d');
    if (ctx) {
        const metrics = @json($metrics);
        const labels = metrics.map(m => new Date(m.timestamp).toLocaleDateString());
        const datasets = {};

        // Group data by metric type
        metrics.forEach(metric => {
            const type = metric.type;
            const value = metric.value;
            if (!datasets[type]) {
                datasets[type] = {
                    label: type.charAt(0).toUpperCase() + type.slice(1).replace('_', ' '),
                    data: [],
                    borderColor: getRandomColor(),
                    fill: false
                };
            }
            if (type === 'blood_pressure') {
                datasets[type].data.push(value.systolic || 0);
            } else if (type === 'heart_rate' || type === 'blood_sugar' || type === 'spo2' || type === 'weight' || type === 'temperature') {
                datasets[type].data.push(value[Object.keys(value)[0]] || 0);
            } else if (type === 'breastfeeding') {
                datasets[type].data.push(value.duration || 0);
            }
        });

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: Object.values(datasets)
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: '{{ __('Value') }}'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '{{ __('Date') }}'
                        }
                    }
                }
            }
        });

        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            let color = '#';
            for (let i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }
    }
</script>
</body>
</html>
