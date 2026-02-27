<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Actions\Firebase\VerifyFirebaseIdTokenAction;
use App\Modules\Auth\Actions\Google\FindOrCreateUserFromGooglePayloadAction;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

final class GoogleAuthService
{
    public function __construct(
        private VerifyFirebaseIdTokenAction $verifyToken,
        private FindOrCreateUserFromGooglePayloadAction $findOrCreateUser,
        private AuthFactory $auth,
    ) {}

    public function authenticate(string $idToken): User
    {
        $payload = $this->verifyToken->execute($idToken);
        $user = $this->findOrCreateUser->execute($payload);

        $this->auth->guard()->login($user);

        return $user;
    }
}
