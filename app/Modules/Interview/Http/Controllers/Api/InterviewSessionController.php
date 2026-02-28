<?php

namespace App\Modules\Interview\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Interview\Http\Requests\CompleteInterviewRequest;
use App\Modules\Interview\Http\Resources\InterviewSessionResource;
use App\Modules\Interview\Services\InterviewSessionService;
use Illuminate\Http\JsonResponse;

final class InterviewSessionController extends Controller
{
    public function __construct(
        private InterviewSessionService $service,
    ) {}

    public function complete(CompleteInterviewRequest $request): JsonResponse
    {
        $session = $this->service->complete(
            $request->user(),
            $request->validated('session_id'),
            $request->validated('answers', []),
        );

        return $this->success([
            'session' => (new InterviewSessionResource($session))->toArray($request),
        ]);
    }
}
