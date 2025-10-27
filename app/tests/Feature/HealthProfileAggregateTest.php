<?php

namespace Tests\Feature;

use App\Domains\HealthProfile\Aggregates\HealthProfileAggregate;
use App\Domains\HealthProfile\Events\MetricRecorded;
use App\Enums\MetricType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use function PHPUnit\Framework\assertStringContainsString;

test('HealthProfileAggregate records metric', function (
    MetricType $metricType,
    array $value,
    ?string $notes,
    ?string $photoUrl,
    ?string $timestamp
) {
    $profileUuid = Str::uuid()->toString();
    Context::add('profile', $profileUuid);

    $timestamp = Carbon::parse($timestamp ?? now());

    HealthProfileAggregate::fake()
        ->given([])
        ->when(function (HealthProfileAggregate $aggregate) use (
            $metricType,
            $value,
            $profileUuid,
            $notes,
            $photoUrl,
            $timestamp,
        ) {
            $aggregate->recordMetric(
                type: $metricType,
                value: $value,
                profileUuid: $profileUuid,
                notes: $notes,
                photoUrl: $photoUrl,
                timestamp: $timestamp
            );
        })
        ->assertRecorded([
            new MetricRecorded(
                type: $metricType->value,
                value: $value,
                profileUuid: $profileUuid,
                notes: $notes,
                photoUrl: $photoUrl,
                timestamp: $timestamp
            )
        ]);
})->with([
    'basic metric' => [
        'metricType' => MetricType::BLOOD_PRESSURE,
        'value' => ['systolic' => 120, 'diastolic' => 80],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
    'metric with notes and photo' => [
        'metricType' => MetricType::WEIGHT,
        'value' => ['weight' => 70.5],
        'notes' => 'Morning measurement',
        'photoUrl' => 'https://example.com/photo.jpg',
        'timestamp' => '2025-09-01 08:00:00',
    ],
    'blood pressure' => [
        'metricType' => MetricType::BLOOD_PRESSURE,
        'value' => ['systolic' => 120, 'diastolic' => 80],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
    'heart rate' => [
        'metricType' => MetricType::HEART_RATE,
        'value' => ['bpm' => 72],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
    'blood sugar' => [
        'metricType' => MetricType::BLOOD_SUGAR,
        'value' => ['level' => 95.5],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
    'spo2' => [
        'metricType' => MetricType::SPO2,
        'value' => ['spo2' => 98],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
    'breastfeeding' => [
        'metricType' => MetricType::BREASTFEEDING,
        'value' => ['duration' => 15, 'side' => 'left'],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
    'weight' => [
        'metricType' => MetricType::WEIGHT,
        'value' => ['weight' => 70.5],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
    'temperature' => [
        'metricType' => MetricType::TEMPERATURE,
        'value' => ['temperature' => 36.7],
        'notes' => null,
        'photoUrl' => null,
        'timestamp' => null,
    ],
]);

test('HealthProfileAggregate rejects unexpected measurement keys by metric type', function (
    MetricType $type,
    array $metric,
    array $allowed,
) {
    $profileUuid = Str::uuid()->toString();
    Context::add('profile', $profileUuid);

    $timestamp = now();
    $notes = null;
    $photoUrl = null;

    $response = test()->postJson(route('health.metrics.store'), [
        'type' => $type->value,
        'value' => $metric,
        'notes' => $notes,
        'photoUrl' => $photoUrl,
        'timestamp' => $timestamp,
        'profileUuid' => $profileUuid,
    ]);

    $response->assertStatus(422);
    foreach ($allowed as $key) {
        assertStringContainsString($key, $response->json('message'));
    }
})->with([
    'blood pressure - extra' => [
        'type' => MetricType::BLOOD_PRESSURE,
        'metric' => ['systolic' => 120, 'diastolic' => 80, 'unexpected' => 1],
        'allowed' => ['systolic', 'diastolic'],
    ],
    'blood pressure - less' => [
        'type' => MetricType::BLOOD_PRESSURE,
        'metric' => ['systolic' => 120],
        'allowed' => ['diastolic'],
    ],
    'weight - extra' => [
        'type' => MetricType::WEIGHT,
        'metric' => ['weight' => 70.5, 'foo' => 1],
        'allowed' => ['weight'],
    ],
    'weight - less' => [
        'type' => MetricType::WEIGHT,
        'metric' => ['foo' => 1],
        'allowed' => ['weight'],
    ],
    'heart rate - extra' => [
        'type' => MetricType::HEART_RATE,
        'metric' => ['bpm' => 72, 'extra' => 2],
        'allowed' => ['bpm'],
    ],
    'heart rate - less' => [
        'type' => MetricType::HEART_RATE,
        'metric' => ['extra' => 2],
        'allowed' => ['bpm'],
    ],
    'blood sugar - extra' => [
        'type' => MetricType::BLOOD_SUGAR,
        'metric' => ['level' => 95.5, 'bad' => 1],
        'allowed' => ['level'],
    ],
    'blood sugar - less' => [
        'type' => MetricType::BLOOD_SUGAR,
        'metric' => ['bad' => 1],
        'allowed' => ['level'],
    ],
    'spo2 - extra' => [
        'type' => MetricType::SPO2,
        'metric' => ['spo2' => 98, 'bad' => 1],
        'allowed' => ['spo2'],
    ],
    'spo2 - less' => [
        'type' => MetricType::SPO2,
        'metric' => ['bad' => 1],
        'allowed' => ['spo2'],
    ],
    'breastfeeding - extra' => [
        'type' => MetricType::BREASTFEEDING,
        'metric' => ['duration' => 15, 'side' => 'left', 'bad' => 1],
        'allowed' => ['duration', 'side'],
    ],
    'breastfeeding - less' => [
        'type' => MetricType::BREASTFEEDING,
        'metric' => ['duration' => 15,],
        'allowed' => ['side'],
    ],
    'temperature - extra' => [
        'type' => MetricType::TEMPERATURE,
        'metric' => ['temperature' => 36.7, 'bad' => 1],
        'allowed' => ['temperature'],
    ],
    'temperature - less' => [
        'type' => MetricType::TEMPERATURE,
        'metric' => ['bad' => 1],
        'allowed' => ['temperature'],
    ],
]);
