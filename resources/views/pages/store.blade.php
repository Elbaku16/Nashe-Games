@extends('layouts.app')

@section('title', 'Store')

@section('content')
<div class="top-bar">
    <h1 class="page-title">Store</h1>
    <form action="{{ route('store') }}" method="GET" class="search-form">
        <input type="text" name="search" class="search-bar" placeholder="Search games..." value="{{ $search }}">
    </form>
</div>

@if ($featured)
    <div class="featured-game"
         onclick="window.location='{{ route('store.game', $featured['dealID']) }}'"
         style="background-image: linear-gradient(135deg, rgba(15,15,15,0.9) 0%, rgba(124,58,237,0.55) 60%, rgba(236,72,153,0.4) 100%), url('{{ $featured['thumb'] }}');">
        <div class="featured-content">
            <div class="featured-left">
                <div class="featured-title">{{ $featured['title'] }}</div>
                <div class="price-tag">
                    AVAILABLE NOW &nbsp;
                    @if ($featured['salePrice'] !== $featured['normalPrice'])
                        <span class="old-price">${{ $featured['normalPrice'] }}</span>
                    @endif
                    <strong>${{ $featured['salePrice'] }}</strong>
                </div>
                <div class="desc">
                    @if (! empty($featured['savings']) && (float) $featured['savings'] > 0)
                        ¡{{ number_format((float) $featured['savings'], 0) }}% de descuento! Oferta destacada de la semana.
                    @else
                        Oferta destacada de la semana en Nashe Games.
                    @endif
                </div>
            </div>
            <form action="{{ route('cart.add') }}" method="POST" onclick="event.stopPropagation()">
                @csrf
                <input type="hidden" name="deal_id" value="{{ $featured['dealID'] }}">
                <button type="submit" class="add-cart-btn">
                    Add to cart <i class="bi bi-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>
@endif

<h3 class="section-title">
    {{ $search !== '' ? 'Resultados para: '.$search : 'New tendencies' }}
</h3>

@if (empty($deals))
    <p class="empty-message">
        @if ($search !== '')
            No se encontraron juegos para "{{ $search }}". Intenta con otro término.
        @else
            No se encontraron juegos. Intenta más tarde.
        @endif
    </p>
@else
    <div class="store-grid">
        @foreach ($deals as $deal)
            <div class="store-card"
                 onclick="window.location='{{ route('store.game', $deal['dealID']) }}'"
                 style="background-image: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.92) 100%), url('{{ $deal['thumb'] }}');">
                <div class="store-card-overlay">
                    <div class="store-card-title">{{ $deal['title'] }}</div>
                    <div class="store-card-footer">
                        <div class="store-card-price">
                            @if ($deal['salePrice'] !== $deal['normalPrice'])
                                <span class="old-price">${{ $deal['normalPrice'] }}</span>
                            @endif
                            <span class="current-price">${{ $deal['salePrice'] }}</span>
                        </div>
                        <form action="{{ route('cart.add') }}" method="POST" onclick="event.stopPropagation()">
                            @csrf
                            <input type="hidden" name="deal_id" value="{{ $deal['dealID'] }}">
                            <button type="submit" class="card-add-btn" title="Agregar al carrito">
                                <i class="bi bi-cart-plus"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection