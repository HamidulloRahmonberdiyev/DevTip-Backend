<?php

namespace App\Modules\Auth\Actions\Firebase;

use App\Modules\Auth\DTOs\GoogleUserPayload;
use App\Modules\Auth\Exceptions\InvalidGoogleTokenException;
use Firebase\Auth\Token\Exception\InvalidToken;
use Firebase\Auth\Token\Exception\UnknownKey;
use Firebase\Auth\Token\Verifier;
use Illuminate\Support\Facades\Config;
use Lcobucci\JWT\Token;

final class VerifyFirebaseIdTokenAction
{
    public function __construct(
        private GooglePublicKeyStore $keyStore,
    ) {}

    public function execute(string $idToken): GoogleUserPayload
    {
        $projectId = Config::get('services.firebase.project_id');

        if (!$projectId) {
            throw InvalidGoogleTokenException::firebaseNotConfigured();
        }

        try {
            $verifier = new Verifier($projectId, $this->keyStore);
            $token = $verifier->verifyIdToken($idToken);
        } catch (InvalidToken|UnknownKey $e) {
            throw InvalidGoogleTokenException::invalidToken();
        }

        $payload = $this->tokenToPayload($token);

        return GoogleUserPayload::fromFirebasePayload($payload);
    }

    /**
     * @return array<string, mixed>
     */
    private function tokenToPayload(Token $token): array
    {
        $get = function (string $claim) use ($token) {
            try {
                return $token->getClaim($claim);
            } catch (\OutOfBoundsException) {
                return null;
            }
        };

        return [
            'sub' => $get('sub'),
            'email' => $get('email'),
            'name' => $get('name'),
            'picture' => $get('picture'),
        ];
    }
}
