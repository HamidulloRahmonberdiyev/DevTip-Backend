<?php

namespace App\Modules\Question\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GetQuestionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'level_id' => ['required', 'integer', Rule::exists('levels', 'id')],
            'lang_id' => ['required', 'integer', Rule::exists('languages', 'id')],
            'technology_id' => ['required', 'integer', Rule::exists('technologies', 'id')],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function getLimit(): int
    {
        return (int) $this->input('limit', 10);
    }
}
