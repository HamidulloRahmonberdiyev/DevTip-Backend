<?php

namespace App\Modules\Auth\Exceptions;

use Exception;

final class InvalidGoogleTokenException extends Exception
{
    public static function firebaseNotConfigured(): self
    {
        return new self('Firebase project ID is not configured on the server.', 500);
    }

    public static function invalidToken(): self
    {
        return new self('Invalid authentication token.', 401);
    }

    public static function missingUserInfo(): self
    {
        return new self('Authentication token does not contain required user information.', 422);
    }
}
