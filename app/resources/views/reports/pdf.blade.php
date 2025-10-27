<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Health Metrics Report') }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { font-size: 24px; margin-bottom: 20px; }
        h2 { font-size: 18px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .date-range { margin-bottom: 20px; }
    </style>
</head>
<body>
<h1>{{ __('Health Metrics Report') }}</h1>
<p class="date-range">{{ __('Date Range') }}: {{ $start_date }} {{ __('to') }} {{ $end_date }}</p>

<!-- Metrics Table -->
<table>
    <thead>
    <tr>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Value') }}</th>
        <th>{{ __('Timestamp') }}</th>
        <th>{{ __('Notes') }}</th>
        <th>{{ __('Photo') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($metrics as $metric)
        <tr>
            <td>{{ __($metric->type) }}</td>
            <td>{{ json_encode($metric->value) }}</td>
            <td>{{ $metric->timestamp->format('Y-m-d H:i:s') }}</td>
            <td>{{ $metric->notes ?? '-' }}</td>
            <td>{{ $metric->photo_url ?? '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
