<?php

namespace App\Http\Controllers;

use App\Services\CheapSharkService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function __construct(private CheapSharkService $cheapShark) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        if ($search !== '') {
            $deals = $this->cheapShark->getDeals(pageSize: 18, title: $search);
            $featured = null;
        } else {
            $deals = $this->cheapShark->getDeals(pageSize: 13);
            $featured = $deals[0] ?? null;
            if ($featured) {
                $deals = array_slice($deals, 1);
            }
        }

        return view('pages.store', [
            'featured' => $featured,
            'deals' => $deals,
            'search' => $search,
        ]);
    }

    public function show(string $dealID): View
    {
        $deal = $this->cheapShark->getDeal($dealID);

        abort_if($deal === null || empty($deal['gameInfo']), 404);

        return view('pages.game', [
            'deal' => $deal,
            'dealID' => $dealID,
        ]);
    }
}
