@extends('layouts.app')

@section('title', $deal['gameInfo']['name'] ?? 'Game detail')

@section('content')
@php
    $info = $deal['gameInfo'];
    $cheapestPrice = $deal['cheapestPrice'] ?? null;
@endphp

<div class="top-bar">
    <a href="{{ route('store') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Back to store
    </a>
</div>

<div class="game-detail">
    <div class="game-detail-hero"
         style="background-image: linear-gradient(180deg, rgba(15,15,15,0.4) 0%, rgba(15,15,15,0.95) 100%), url('{{ $info['thumb'] ?? '' }}');">
        <h1 class="game-detail-title">{{ $info['name'] }}</h1>
    </div>

    <div class="game-detail-body">
        <div class="game-detail-price-box">
            <div class="price-row">
                <span class="price-label">Precio actual:</span>
                <span class="current-price big">${{ $info['salePrice'] }}</span>
            </div>
            @if (! empty($info['retailPrice']) && (float) $info['retailPrice'] > (float) $info['salePrice'])
                <div class="price-row">
                    <span class="price-label">Precio normal:</span>
                    <span class="old-price">${{ $info['retailPrice'] }}</span>
                </div>
            @endif
            @if ($cheapestPrice)
                <div class="price-row">
                    <span class="price-label">Precio histórico más bajo:</span>
                    <span class="historic-price">${{ $cheapestPrice['price'] ?? '—' }}</span>
                </div>
            @endif

            <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="deal_id" value="{{ $dealID }}">
                <button type="submit" class="auth-submit-btn">
                    <i class="bi bi-cart-plus"></i> Add to cart
                </button>
            </form>
        </div>

        @if (! empty($deal['cheaperStores']))
            <div class="cheaper-stores">
                <h3 class="section-title">Otras tiendas con precios similares</h3>
                <ul class="store-list">
                    @foreach (array_slice($deal['cheaperStores'], 0, 5) as $store)
                        <li>Store ID {{ $store['storeID'] }} — <strong>${{ $store['salePrice'] }}</strong> <span class="old-price">${{ $store['retailPrice'] }}</span></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
