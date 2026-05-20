<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Services\CheapSharkService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CheapSharkService $cheapShark) {}

    public function index(): View
    {
        $items = Auth::user()->cartItems()->latest()->get();
        $total = $items->sum(fn (CartItem $item) => (float) $item->sale_price);

        return view('pages.cart', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'deal_id' => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        if ($user->cartItems()->where('deal_id', $data['deal_id'])->exists()) {
            return back()->with('status', 'Este juego ya está en tu carrito.');
        }

        if ($user->libraryItems()->where('deal_id', $data['deal_id'])->exists()) {
            return back()->with('status', 'Ya tienes este juego en tu biblioteca.');
        }

        $deal = $this->cheapShark->getDeal($data['deal_id']);

        if (! $deal || empty($deal['gameInfo'])) {
            return back()->withErrors(['deal' => 'No se pudo cargar la información del juego.']);
        }

        $info = $deal['gameInfo'];

        $user->cartItems()->create([
            'deal_id' => $data['deal_id'],
            'game_id' => $info['gameID'] ?? null,
            'title' => $info['name'] ?? 'Juego sin título',
            'thumb' => $info['thumb'] ?? null,
            'sale_price' => (float) ($info['salePrice'] ?? 0),
            'normal_price' => (float) ($info['retailPrice'] ?? 0),
        ]);

        return redirect()->route('cart')->with('status', 'Juego agregado al carrito.');
    }

    public function remove(CartItem $cartItem): RedirectResponse
    {
        abort_if($cartItem->user_id !== Auth::id(), 403);

        $cartItem->delete();

        return back()->with('status', 'Juego eliminado del carrito.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer'],
        ]);

        $user = Auth::user();

        DB::transaction(function () use ($user, $data) {
            $items = $user->cartItems()->whereIn('id', $data['item_ids'])->get();

            foreach ($items as $item) {
                $user->libraryItems()->create([
                    'deal_id' => $item->deal_id,
                    'game_id' => $item->game_id,
                    'title' => $item->title,
                    'thumb' => $item->thumb,
                    'purchase_price' => $item->sale_price,
                    'purchased_at' => now(),
                ]);
                $item->delete();
            }
        });

        return redirect()->route('library')->with('status', '¡Compra realizada con éxito! Disfruta tus juegos.');
    }
}
