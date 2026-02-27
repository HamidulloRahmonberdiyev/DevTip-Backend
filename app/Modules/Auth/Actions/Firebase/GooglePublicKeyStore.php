<?php

namespace App\Modules\Auth\Actions\Firebase;

use Firebase\Auth\Token\Domain\KeyStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class GooglePublicKeyStore implements KeyStore
{
    private const KEYS_URL = 'https://www.googleapis.com/service_accounts/v1/metadata/x509/securetoken@system.gserviceaccount.com';

    private const CACHE_KEY = 'firebase_id_token_public_keys';

    private const CACHE_TTL_SECONDS = 3600;

    public function get($keyId): string
    {
        $keys = $this->fetchKeys();

        if (!isset($keys[$keyId])) {
            throw new \OutOfBoundsException("Unknown key: {$keyId}");
        }

        return $keys[$keyId];
    }

    /**
     * @return array<string, string>
     */
    private function fetchKeys(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function (): array {
            $response = Http::get(self::KEYS_URL);

            if (!$response->successful()) {
                throw new \RuntimeException('Failed to fetch Firebase public keys.');
            }

            $data = $response->json();

            return is_array($data) ? $data : [];
        });
    }
}
