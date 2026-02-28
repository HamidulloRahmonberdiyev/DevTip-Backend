<?php

namespace App\Modules\Technology\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Technology\Models\Technology;
use Illuminate\Http\JsonResponse;

final class TechnologyController extends Controller
{
    public function index(): JsonResponse
    {
        $technologies = Technology::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'image']);

        return $this->success([
            'technologies' => $technologies,
        ]);
    }
}
