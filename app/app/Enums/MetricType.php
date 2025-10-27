<?php

namespace App\Enums;

use App\Enums\Traits\EnumHelpers;

enum MetricType: string
{
    use EnumHelpers;

    case BLOOD_PRESSURE = 'blood_pressure';
    case HEART_RATE = 'heart_rate';
    case BLOOD_SUGAR = 'blood_sugar';
    case SPO2 = 'spo2';
    case BREASTFEEDING = 'breastfeeding';
    case WEIGHT = 'weight';
    case TEMPERATURE = 'temperature';

    /**
     * @param MetricType $metricType
     * @return array{
     *     'value-rules': array{
     *         'required',
     *         'array',
     *         \Closure,
     *     }
     *     'sub-rules': string,
     * }
     */
    public static function metricTypeRule(MetricType $metricType): array
    {
        return match($metricType) {
            MetricType::BLOOD_PRESSURE => [
                'value-rules' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        $allowed = ['systolic', 'diastolic'];
                        if (array_diff(array_keys($value), $allowed)) {
                            $fail('Unexpected keys for blood pressure. Allowed keys are: ' . implode(', ', $allowed));
                        }
                    },
                ],
                'sub-rules' => [
                    'value.systolic' => 'required|integer|min:50|max:250',
                    'value.diastolic' => 'required|integer|min:30|max:150',
                ],
            ],
            MetricType::WEIGHT => [
                'value-rules' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        $allowed = ['weight'];
                        if (array_diff(array_keys($value), $allowed)) {
                            $fail('Unexpected keys for weight. Allowed keys are: ' . implode(', ', $allowed));
                        }
                    },
                ],
                'sub-rules' => [
                    'value.weight' => 'required|numeric|min:1|max:500',
                ],
            ],
            MetricType::HEART_RATE => [
                'value-rules' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        $allowed = ['bpm'];
                        if (array_diff(array_keys($value), $allowed)) {
                            $fail('Unexpected keys for heart rate. Allowed keys are: ' . implode(', ', $allowed));
                        }
                    },
                ],
                'sub-rules' => [
                    'value.bpm' => 'required|integer|min:30|max:220',
                ],
            ],
            MetricType::BLOOD_SUGAR => [
                'value-rules' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        $allowed = ['level'];
                        if (array_diff(array_keys($value), $allowed)) {
                            $fail('Unexpected keys for blood sugar. Allowed keys are: ' . implode(', ', $allowed));
                        }
                    },
                ],
                'sub-rules' => [
                    'value.level' => 'required|numeric|min:20|max:600',
                ],
            ],
            MetricType::SPO2 => [
                'value-rules' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        $allowed = ['spo2'];
                        if (array_diff(array_keys($value), $allowed)) {
                            $fail('Unexpected keys for SpO2. Allowed keys are: ' . implode(', ', $allowed));
                        }
                    },
                ],
                'sub-rules' => [
                    'value.spo2' => 'required|integer|min:70|max:100',
                ],
            ],
            MetricType::BREASTFEEDING => [
                'value-rules' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        $allowed = ['duration', 'side'];
                        if (array_diff(array_keys($value), $allowed)) {
                            $fail('Unexpected keys for breastfeeding. Allowed keys are: ' . implode(', ', $allowed));
                        }
                    },
                ],
                'sub-rules' => [
                    'value.duration' => 'required|integer|min:1|max:240',
                    'value.side' => 'required|in:left,right,both',
                ],
            ],
            MetricType::TEMPERATURE => [
                'value-rules' => [
                    'required',
                    'array',
                    function ($attribute, $value, $fail) {
                        $allowed = ['temperature'];
                        if (array_diff(array_keys($value), $allowed)) {
                            $fail('Unexpected keys for temperature. Allowed keys are: ' . implode(', ', $allowed));
                        }
                    },
                ],
                'sub-rules' => [
                    'value.temperature' => 'required|numeric|min:30|max:85',
                ],
            ],
        };
    }
}
