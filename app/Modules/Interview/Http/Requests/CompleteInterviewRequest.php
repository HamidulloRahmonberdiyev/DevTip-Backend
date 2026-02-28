<?php

namespace App\Modules\Interview\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class CompleteInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'integer', Rule::exists('interview_sessions', 'id')],
            'answers' => ['sometimes', 'array'],
            'answers.*.question_id' => ['required', 'integer', Rule::exists('questions', 'id')],
            'answers.*.answer' => ['required', 'string'],
            'answers.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
