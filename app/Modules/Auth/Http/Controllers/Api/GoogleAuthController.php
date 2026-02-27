<?php

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Exceptions\InvalidGoogleTokenException;
use App\Modules\Auth\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class GoogleAuthController extends Controller
{
    public function __construct(
        private GoogleAuthService $googleAuth,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_token' => ['required', 'string'],
        ]);

        try {
            $user = $this->googleAuth->authenticate($validated['id_token']);
        } catch (InvalidGoogleTokenException $e) {
            $status = (int) $e->getCode() ?: 401;
            return $this->error($e->getMessage(), $status);
        }

        $request->session()->regenerate();

        return $this->success(['user' => $this->userResource($user)]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->unauthorized('Unauthenticated.');
        }
        return $this->success(['user' => $this->userResource($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->success(['message' => 'Logged out.']);
    }

    private function userResource(\App\Models\User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ];
    }
}
