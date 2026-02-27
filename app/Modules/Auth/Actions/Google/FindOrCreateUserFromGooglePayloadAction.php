<?php

namespace App\Modules\Auth\Actions\Google;

use App\Modules\Auth\DTOs\GoogleUserPayload;
use App\Models\User;

final class FindOrCreateUserFromGooglePayloadAction
{
    public function execute(GoogleUserPayload $payload): User
    {
        return User::updateOrCreate(
            ['email' => $payload->email],
            [
                'name' => $payload->name,
                'google_id' => $payload->googleId,
                'avatar' => $payload->avatar,
            ],
        );
    }
}
