<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente para la Steam Store API pública + helper de URLs del CDN de Steam.
 *
 * Docs (no oficial): https://wiki.teamfortress.com/wiki/User:RJackson/StorefrontAPI
 */
class SteamService
{
    private const STORE_BASE = 'https://store.steampowered.com/api/appdetails';

    private const CDN_BASE = 'https://shared.cloudflare.steamstatic.com/store_item_assets/steam/apps';

    private const CACHE_TTL = 86400;

    /**
     * Construye una URL del CDN de Steam para un appID y variante de imagen.
     *
     * Variantes soportadas:
     *  - 'header'   → 460x215 (horizontal, banners)
     *  - 'library'  → 600x900 (vertical, cards estilo grid)
     *  - 'capsule'  → 231x87  (capsule chico)
     *  - 'hero'     → 1920x620 (hero ancho)
     */
    public function imageUrl(?string $appID, string $variant = 'header'): ?string
    {
        if ($appID === null || $appID === '') {
            return null;
        }

        $file = match ($variant) {
            'header' => 'header.jpg',
            'library' => 'library_600x900.jpg',
            'capsule' => 'capsule_231x87.jpg',
            'hero' => 'library_hero.jpg',
            default => 'header.jpg',
        };

        return self::CDN_BASE."/{$appID}/{$file}";
    }

    /**
     * Trae detalles del juego desde la Steam Store API (descripción, géneros, etc).
     *
     * @return array<string, mixed>|null
     */
    public function getDetails(?string $appID, string $language = 'spanish', string $countryCode = 'mx'): ?array
    {
        if ($appID === null || $appID === '') {
            return null;
        }

        $cacheKey = "steam:details:{$appID}:{$language}:{$countryCode}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($appID, $language, $countryCode) {
            try {
                $response = Http::timeout(10)->get(self::STORE_BASE, [
                    'appids' => $appID,
                    'l' => $language,
                    'cc' => $countryCode,
                ]);

                if (! $response->successful()) {
                    return null;
                }

                $payload = $response->json();
                $entry = $payload[$appID] ?? null;

                if (! is_array($entry) || ($entry['success'] ?? false) !== true) {
                    return null;
                }

                return $entry['data'] ?? null;
            } catch (\Throwable $e) {
                Log::error('Steam Store request error', ['appID' => $appID, 'message' => $e->getMessage()]);

                return null;
            }
        });
    }
}
