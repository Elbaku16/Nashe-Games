@extends('layouts.app')

@section('title', 'Tienda')

@section('content')
<div class="top-bar">
    <h1 class="page-title">Tienda</h1>
    <form action="{{ route('store') }}" method="GET" class="search-form">
        <input type="text" name="search" class="search-bar" placeholder="Buscar juegos..." value="{{ $search }}">
    </form>
</div>

@if ($featured)
    <div class="featured-game"
         style="background-image: linear-gradient(135deg, rgba(15,15,15,0.9) 0%, rgba(124,58,237,0.55) 60%, rgba(236,72,153,0.4) 100%), url('{{ $featured['bannerImage'] }}');">
        <a class="card-link-overlay" href="{{ route('store.game', $featured['dealID']) }}" aria-label="{{ $featured['title'] }}"></a>
        <div class="featured-content">
            <div class="featured-left">
                <div class="featured-title">{{ $featured['title'] }}</div>
                <div class="price-tag">
                    DISPONIBLE AHORA &nbsp;
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
        </div>
    </div>
@endif

<h3 class="section-title">
    {{ $search !== '' ? 'Resultados para: '.$search : 'Nuevas tendencias' }}
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
                 style="background-image: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.92) 100%), url('{{ $deal['cardImage'] }}');">
                <a class="card-link-overlay" href="{{ route('store.game', $deal['dealID']) }}" aria-label="{{ $deal['title'] }}"></a>
                <div class="store-card-overlay">
                    <div class="store-card-title">{{ $deal['title'] }}</div>
                    <div class="store-card-footer">
                        <div class="store-card-price">
                            @if ($deal['salePrice'] !== $deal['normalPrice'])
                                <span class="old-price">${{ $deal['normalPrice'] }}</span>
                            @endif
                            <span class="current-price">${{ $deal['salePrice'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection