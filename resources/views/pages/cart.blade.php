@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<div class="top-bar">
    <h1 class="page-title">My cart</h1>
</div>

@if ($items->isEmpty())
    <div class="empty-state">
        <i class="bi bi-cart-x"></i>
        <p>Tu carrito está vacío. Visita la <a href="{{ route('store') }}">tienda</a> para añadir juegos.</p>
    </div>
@else
    <form action="{{ route('cart.checkout') }}" method="POST" id="cart-form">
        @csrf
        <div class="cart-list">
            @foreach ($items as $item)
                <label class="cart-item selected" data-price="{{ $item->sale_price }}">
                    <input type="checkbox"
                           name="item_ids[]"
                           value="{{ $item->id }}"
                           checked
                           class="cart-checkbox"
                           onchange="updateTotal(this)">
                    <span class="checkbox checked"></span>

                    <div class="cart-img"
                         style="background-image: url('{{ $item->thumb }}');"></div>

                    <div class="cart-name">{{ $item->title }}</div>

                    <div class="cart-price">
                        @if ((float) $item->sale_price < (float) $item->normal_price)
                            <span class="old-price">${{ $item->normal_price }}</span>
                        @endif
                        ${{ $item->sale_price }}
                    </div>

                    <button type="button"
                            class="cart-remove-btn"
                            onclick="document.getElementById('remove-{{ $item->id }}').submit()"
                            title="Eliminar del carrito">
                        <i class="bi bi-trash"></i>
                    </button>
                </label>
            @endforeach
        </div>

        <div class="pay-now-container">
            <div class="total-display">
                Total: <span id="total-amount">${{ number_format($total, 2) }}</span>
            </div>
            <button type="submit" class="pay-btn">Pay Now</button>
        </div>
    </form>

    {{-- Formularios de eliminación (separados para no anidar forms) --}}
    @foreach ($items as $item)
        <form action="{{ route('cart.remove', $item) }}" method="POST" id="remove-{{ $item->id }}" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <script>
        function updateTotal(checkbox) {
            // Alternar la clase visual del item
            const item = checkbox.closest('.cart-item');
            const visualCheckbox = item.querySelector('.checkbox');
            item.classList.toggle('selected', checkbox.checked);
            visualCheckbox.classList.toggle('checked', checkbox.checked);

            // Recalcular total
            let total = 0;
            document.querySelectorAll('.cart-checkbox:checked').forEach(cb => {
                const parent = cb.closest('.cart-item');
                total += parseFloat(parent.dataset.price);
            });
            document.getElementById('total-amount').textContent = '$' + total.toFixed(2);
        }
    </script>
@endif
@endsection
