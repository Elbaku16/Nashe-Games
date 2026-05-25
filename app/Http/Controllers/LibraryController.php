<?php

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use App\Services\CheapSharkService;
use App\Services\SteamService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function __construct(
        private CheapSharkService $cheapShark,
        private SteamService $steam,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $query = Auth::user()->libraryItems()->latest('purchased_at');

        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        $items = $query->get()->map(function (LibraryItem $item): LibraryItem {
            $item->displayImage = $this->resolveImage($item);

            return $item;
        });

        return view('pages.library', [
            'items' => $items,
            'search' => $search,
        ]);
    }

    private function resolveImage(LibraryItem $item): ?string
    {
        if ($item->game_id === null || $item->game_id === '') {
            return $item->thumb;
        }

        $appID = Cache::remember(
            "library:steamAppID:{$item->game_id}",
            86400,
            fn () => $this->cheapShark->getGame($item->game_id)['info']['steamAppID'] ?? null,
        );

        return $this->steam->imageUrl($appID, 'library') ?? $item->thumb;
    }

    public function uninstall(LibraryItem $libraryItem): RedirectResponse
    {
        abort_if($libraryItem->user_id !== Auth::id(), 403);

        $title = $libraryItem->title;
        $libraryItem->delete();

        return back()->with('status', "Has desinstalado \"{$title}\" de tu biblioteca.");
    }
}
