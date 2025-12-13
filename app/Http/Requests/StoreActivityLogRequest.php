<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityLogRequest extends FormRequest
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
        $familyId = $this->user()->family_id;

        return [
            'activity_id' => ['required', 'exists:activities,id'],
            'user_id' => ['required', 'exists:users,id'],
            'performed_at' => ['required', 'date'],
            'sets' => ['nullable', 'integer', 'min:1', 'max:100'],
            'reps' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'duration_seconds' => ['nullable', 'integer', 'min:1', 'max:86400'],
            'distance' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'user_id.required' => 'Please select a family member.',
            'user_id.exists' => 'The selected family member does not exist.',
            'performed_at.required' => 'Please enter when the activity was performed.',
            'performed_at.date' => 'Please enter a valid date.',
        ];
    }
}
