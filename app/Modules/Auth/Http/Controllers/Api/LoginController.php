<?php

namespace App\Modules\Auth\Http\Controllers\Api;

use App\Enums\TokenAbility;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Auth\Http\Requests\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

final class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return $this->unauthorized('Email yoki parol noto\'g\'ri.');
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
