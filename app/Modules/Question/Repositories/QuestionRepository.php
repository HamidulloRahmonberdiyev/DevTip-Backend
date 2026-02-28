<?php

namespace App\Modules\Question\Repositories;

use App\Modules\Question\Models\Question;
use Illuminate\Database\Eloquent\Collection;

final class QuestionRepository
{
    /**
     * @param  array<int, int>  $excludeQuestionIds  Userni oxirgi natijasidagi savol ID lari (ularni olmaydi)
     */
    public function getRandomForQuiz(
        int $levelId,
        int $langId,
        int $technologyId,
        int $limit,
        array $excludeQuestionIds = [],
    ): Collection {
        $query = Question::query()
            ->forQuiz($levelId, $langId, $technologyId);

        if ($excludeQuestionIds !== []) {
            $query->whereNotIn('id', $excludeQuestionIds);
        }

        return $query
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }
}
