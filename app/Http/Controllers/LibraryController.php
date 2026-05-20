<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LibraryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $query = Auth::user()->libraryItems()->latest('purchased_at');

        if ($search !== '') {
            $query->where('title', 'like', '%'.$search.'%');
        }

        $items = $query->get();

        return view('pages.library', [
            'items' => $items,
            'search' => $search,
        ]);
    }
}
