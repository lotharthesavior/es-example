<?php

namespace App\Http\Requests;

use App\Domains\HealthProfile\Commands\StoreHealthMetricCommand;
use App\Enums\MetricType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;

class StoreMetricRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $metricRules = MetricType::metricTypeRule(MetricType::tryFrom($this->string('type')));

        return [
            'type' => 'required|in:' . implode(',', MetricType::values()),
            'value' => $metricRules['value-rules'],
            'notes' => 'nullable|string|max:1000',
            'photo_url' => 'nullable|url',
            'timestamp' => 'nullable|date',
        ] + $metricRules['sub-rules'];
    }

    public function getTimestamp(): Carbon
    {
        return $this->input('timestamp') ? Carbon::parse($this->input('timestamp')) : now();
    }

    public function getCommand(): StoreHealthMetricCommand
    {
        return new StoreHealthMetricCommand(
            metricUuid: $this->metric?->uuid ?? Str::uuid()->toString(),
            profileUuid: Context::get('profile'),
            type: MetricType::from($this->input('type')),
            value: $this->input('value'),
            notes: $this->input('notes'),
            photoUrl: $this->input('photo_url'),
            timestamp: $this->getTimestamp(),
        );
    }
}
