<?php

namespace App\Modules\Question\Http\Requests;

use App\Modules\Question\Models\QuestionRating;
use Illuminate\Foundation\Http\FormRequest;

final class RateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stars' => [
                'required',
                'integer',
                'min:' . QuestionRating::MIN_STARS,
                'max:' . QuestionRating::MAX_STARS,
            ],
        ];
    }
}
