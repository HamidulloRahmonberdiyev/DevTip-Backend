<?php

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Exceptions\InvalidGoogleTokenException;
use App\Modules\Auth\Services\GoogleAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class GoogleAuthController extends Controller
{
    public function __construct(
        private GoogleAuthService $googleAuth,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $request->validate(['id_token' => ['required', 'string']]);

        try {
            $user = $this->googleAuth->authenticate($request->input('id_token'));
        } catch (InvalidGoogleTokenException $e) {
            return $this->error($e->getMessage(), (int) $e->getCode() ?: 401);
        }

        $accessTtlDays = config('sanctum.access_token_ttl_days', 1);
        $refreshTtlDays = config('sanctum.refresh_token_ttl_days', 30);

        $accessToken = $user->createToken(
            'access-token',
            [TokenAbility::AccessApi->value],
            now()->addDays($accessTtlDays)
        );
        $refreshToken = $user->createToken(
            'refresh-token',
            [TokenAbility::Refresh->value],
            now()->addDays($refreshTtlDays)
        );

        return $this->success([
            'user' => $this->userResource($user),
            'access_token' => $accessToken->plainTextToken,
            'refresh_token' => $refreshToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $accessTtlDays * 86400,
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $accessTtlDays = config('sanctum.access_token_ttl_days', 1);
        $accessToken = $request->user()->createToken(
            'access-token',
            [TokenAbility::AccessApi->value],
            now()->addDays($accessTtlDays)
        );

        return $this->success([
            'access_token' => $accessToken->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => $accessTtlDays * 86400,
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(['user' => $this->userResource($request->user())]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->tokens()->delete();

        return $this->success(['message' => 'Logged out.']);
    }

    private function userResource(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ];
    }
}
