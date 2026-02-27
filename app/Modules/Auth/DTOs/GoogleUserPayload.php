<?php

namespace App\Modules\Auth\DTOs;

final readonly class GoogleUserPayload
{
    public function __construct(
        public string $googleId,
        public string $email,
        public string $name,
        public ?string $avatar,
    ) {}

    public static function fromTokenInfoArray(array $payload): self
    {
        $googleId = $payload['sub'] ?? null;
        $email = $payload['email'] ?? null;

        if (!$googleId || !$email) {
            throw \App\Modules\Auth\Exceptions\InvalidGoogleTokenException::missingUserInfo();
        }

        return new self(
            googleId: $googleId,
            email: $email,
            name: $payload['name'] ?? $email,
            avatar: $payload['picture'] ?? null,
        );
    }

    public static function fromFirebasePayload(array $payload): self
    {
        return self::fromTokenInfoArray($payload);
    }
}
