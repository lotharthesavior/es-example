<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LogMedicationRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'medication_id' => 'required|string',
            'taken' => 'required|boolean',
        ];
    }
}
