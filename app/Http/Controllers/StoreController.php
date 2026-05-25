<?php

namespace App\Http\Controllers;

use App\Services\CheapSharkService;
use App\Services\SteamService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(
        private CheapSharkService $cheapShark,
        private SteamService $steam,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $deals = $this->cheapShark->getDeals(pageSize: 60, title: $search);

            // CheapShark hace matching laxo; filtramos para que el título
            // contenga la frase buscada (case-insensitive, ignorando acentos).
            $needle = mb_strtolower($search);
            $deals = array_values(array_filter($deals, function (array $deal) use ($needle): bool {
                $title = mb_strtolower((string) ($deal['title'] ?? ''));

                return str_contains($title, $needle);
            }));

            $featured = null;
        } else {
            $deals = $this->cheapShark->getDeals(pageSize: 13);
            $featured = $deals[0] ?? null;
            if ($featured) {
                $deals = array_slice($deals, 1);
            }
        }

        $deals = array_map(fn (array $deal) => $this->withSteamImages($deal), $deals);

        if ($featured) {
            $featured = $this->withSteamImages($featured);
        }

        return view('pages.store', [
            'featured' => $featured,
            'deals' => $deals,
            'search' => $search,
        ]);
    }

    /**
     * Inyecta URLs de imagen en alta resolución desde el CDN de Steam,
     * con fallback al thumb de CheapShark cuando no hay steamAppID.
     *
     * @param  array<string, mixed>  $deal
     * @return array<string, mixed>
     */
    private function withSteamImages(array $deal): array
    {
        $appID = $deal['steamAppID'] ?? null;
        $thumb = $deal['thumb'] ?? null;

        $deal['cardImage'] = $this->steam->imageUrl($appID, 'library') ?? $thumb;
        $deal['bannerImage'] = $this->steam->imageUrl($appID, 'header') ?? $thumb;

        return $deal;
    }

    public function show(string $dealID): View
    {
        $deal = $this->cheapShark->getDeal($dealID);

        abort_if($deal === null || empty($deal['gameInfo']), 404);

        $steamAppID = $deal['gameInfo']['steamAppID'] ?? null;
        $steamDetails = $this->steam->getDetails($steamAppID);
        $heroImage = $this->steam->imageUrl($steamAppID, 'hero')
            ?? $this->steam->imageUrl($steamAppID, 'header')
            ?? ($deal['gameInfo']['thumb'] ?? null);

        return view('pages.game', [
            'deal' => $deal,
            'dealID' => $dealID,
            'steamDetails' => $steamDetails,
            'heroImage' => $heroImage,
        ]);
    }
}
