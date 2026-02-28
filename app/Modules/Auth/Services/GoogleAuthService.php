<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Actions\Firebase\VerifyFirebaseIdTokenAction;
use App\Modules\Auth\Actions\Google\FindOrCreateUserFromGooglePayloadAction;

final class GoogleAuthService
{
    public function __construct(
        private VerifyFirebaseIdTokenAction $verifyToken,
        private FindOrCreateUserFromGooglePayloadAction $findOrCreateUser,
    ) {}

    public function authenticate(string $idToken): User
    {
        $payload = $this->verifyToken->execute($idToken);

        return $this->findOrCreateUser->execute($payload);
    }
}
