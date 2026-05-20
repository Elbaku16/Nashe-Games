<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente HTTP para la API pública de CheapShark.
 *
 * Documentación: https://apidocs.cheapshark.com/
 */
class CheapSharkService
{
    private const BASE_URL = 'https://www.cheapshark.com/api/1.0';

    private const CACHE_TTL = 600;

    /**
     * Obtiene una lista de ofertas (deals) desde CheapShark.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDeals(int $pageSize = 12, int $pageNumber = 0, ?string $title = null, ?string $storeID = '1'): array
    {
        $cacheKey = 'cheapshark:deals:'.md5(json_encode([$pageSize, $pageNumber, $title, $storeID]));

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($pageSize, $pageNumber, $title, $storeID) {
            $params = [
                'pageSize' => $pageSize,
                'pageNumber' => $pageNumber,
                'sortBy' => 'Deal Rating',
                'AAA' => 1,
            ];

            if ($title !== null && $title !== '') {
                $params['title'] = $title;
                unset($params['AAA']);
            }

            if ($storeID !== null && $storeID !== '') {
                $params['storeID'] = $storeID;
            }

            try {
                $response = Http::timeout(15)->get(self::BASE_URL.'/deals', $params);

                if (! $response->successful()) {
                    Log::warning('CheapShark deals request failed', ['status' => $response->status()]);

                    return [];
                }

                return $response->json() ?? [];
            } catch (\Throwable $e) {
                Log::error('CheapShark deals request error', ['message' => $e->getMessage()]);

                return [];
            }
        });
    }

    /**
     * Obtiene los detalles de una oferta concreta por su dealID.
     *
     * @return array<string, mixed>|null
     */
    public function getDeal(string $dealID): ?array
    {
        $cacheKey = 'cheapshark:deal:'.$dealID;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($dealID) {
            try {
                $response = Http::timeout(15)->get(self::BASE_URL.'/deals', [
                    'id' => $dealID,
                ]);

                if (! $response->successful()) {
                    return null;
                }

                return $response->json();
            } catch (\Throwable $e) {
                Log::error('CheapShark deal request error', ['message' => $e->getMessage()]);

                return null;
            }
        });
    }

    /**
     * Obtiene información de un juego por gameID de CheapShark.
     *
     * @return array<string, mixed>|null
     */
    public function getGame(string $gameID): ?array
    {
        $cacheKey = 'cheapshark:game:'.$gameID;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($gameID) {
            try {
                $response = Http::timeout(15)->get(self::BASE_URL.'/games', [
                    'id' => $gameID,
                ]);

                if (! $response->successful()) {
                    return null;
                }

                return $response->json();
            } catch (\Throwable $e) {
                Log::error('CheapShark game request error', ['message' => $e->getMessage()]);

                return null;
            }
        });
    }

    /**
     * Devuelve la oferta destacada (la mejor del momento).
     *
     * @return array<string, mixed>|null
     */
    public function getFeaturedDeal(): ?array
    {
        $deals = $this->getDeals(pageSize: 1, pageNumber: 0);

        return $deals[0] ?? null;
    }
}
