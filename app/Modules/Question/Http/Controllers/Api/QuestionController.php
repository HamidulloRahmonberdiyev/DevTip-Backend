<?php

namespace App\Modules\Question\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Question\Http\Requests\GetQuestionsRequest;
use App\Modules\Question\Http\Requests\RateQuestionRequest;
use App\Modules\Question\Http\Resources\QuestionResource;
use App\Modules\Question\Models\Question;
use App\Modules\Question\Services\QuestionService;
use Illuminate\Http\JsonResponse;

final class QuestionController extends Controller
{
    public function __construct(
        private QuestionService $service,
    ) {}

    public function index(GetQuestionsRequest $request): JsonResponse
    {
        $result = $this->service->getQuestionsForQuiz(
            $request->validated('level_id'),
            $request->validated('lang_id'),
            $request->validated('technology_id'),
            $request->getLimit(),
            $request->user(),
        );

        $questions = $result['questions']
            ->map(fn ($q) => (new QuestionResource($q))->toArray($request))
            ->values()
            ->all();

        $data = [
            'questions' => $questions,
            'count' => $result['count'],
        ];
        if ($result['session'] !== null) {
            $data['session_id'] = $result['session']->id;
        }

        return $this->success($data);
    }

    public function rate(Question $question, RateQuestionRequest $request): JsonResponse
    {
        $result = $this->service->rateQuestion(
            $question,
            $request->user(),
            $request->validated('stars'),
        );

        return $this->success($result);
    }
}
