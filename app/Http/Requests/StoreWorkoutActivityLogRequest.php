<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkoutActivityLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'activity_id' => ['required', 'exists:activities,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'sets' => ['required', 'array', 'min:1'],
            'sets.*.set_number' => ['required', 'integer', 'min:1'],
            'sets.*.reps' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'sets.*.weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'sets.*.duration_seconds' => ['nullable', 'integer', 'min:1', 'max:86400'],
            'sets.*.distance' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'sets.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'activity_id.required' => 'Please select an activity.',
            'activity_id.exists' => 'The selected activity does not exist.',
            'sets.required' => 'At least one set is required.',
            'sets.min' => 'At least one set is required.',
        ];
    }
}
