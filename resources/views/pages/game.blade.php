@extends('layouts.app')

@section('title', $deal['gameInfo']['name'] ?? 'Detalle del juego')

@section('content')
@php
    $info = $deal['gameInfo'];
    $cheapestPrice = $deal['cheapestPrice'] ?? null;

    $shortDescription = $steamDetails['short_description'] ?? null;
    $genres = collect($steamDetails['genres'] ?? [])->pluck('description')->filter()->values()->all();
    $developers = $steamDetails['developers'] ?? [];

    $steamRatingPercent = isset($info['steamRatingPercent']) ? (int) $info['steamRatingPercent'] : 0;
    $steamRatingText = $info['steamRatingText'] ?? null;
    $steamRatingCount = isset($info['steamRatingCount']) ? (int) $info['steamRatingCount'] : 0;
    $metacriticScore = isset($info['metacriticScore']) ? (int) $info['metacriticScore'] : 0;
    $metacriticLink = $info['metacriticLink'] ?? null;
    $releaseTimestamp = isset($info['releaseDate']) ? (int) $info['releaseDate'] : 0;
    $releaseDate = $releaseTimestamp > 0 ? date('M j, Y', $releaseTimestamp) : null;
    $publisher = $info['publisher'] ?? null;
    $steamAppID = $info['steamAppID'] ?? null;
    $trailer = $steamDetails['movies'][0] ?? null;

    $steamRatingClass = match (true) {
        $steamRatingPercent >= 80 => 'rating-positive',
        $steamRatingPercent >= 50 => 'rating-mixed',
        $steamRatingPercent > 0   => 'rating-negative',
        default                   => '',
    };

    $metacriticClass = match (true) {
        $metacriticScore >= 75 => 'rating-positive',
        $metacriticScore >= 50 => 'rating-mixed',
        $metacriticScore > 0   => 'rating-negative',
        default                => '',
    };
@endphp

<div class="top-bar">
    <a href="{{ route('store') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Volver a la tienda
    </a>
</div>

<div class="game-detail">
    <div class="game-detail-hero"
         style="background-image: linear-gradient(180deg, rgba(15,15,15,0.4) 0%, rgba(15,15,15,0.95) 100%), url('{{ $heroImage ?? $info['thumb'] ?? '' }}');">
        <h1 class="game-detail-title">{{ $info['name'] }}</h1>
    </div>

    @if ($trailer)
        <div class="game-trailer-wrapper">
            <video class="game-trailer"
                   controls
                   playsinline
                   preload="metadata"
                   poster="{{ $trailer['thumbnail'] ?? '' }}">
                @if (! empty($trailer['mp4']))
                    <source src="{{ $trailer['mp4']['max'] ?? $trailer['mp4']['480'] ?? '' }}" type="video/mp4">
                @endif
                @if (! empty($trailer['webm']))
                    <source src="{{ $trailer['webm']['max'] ?? $trailer['webm']['480'] ?? '' }}" type="video/webm">
                @endif
                Tu navegador no soporta el reproductor de video.
            </video>
        </div>
    @endif

    @if ($shortDescription || ! empty($genres))
        <div class="game-detail-intro">
            @if (! empty($genres))
                <div class="genre-chips">
                    @foreach ($genres as $genre)
                        <span class="genre-chip">{{ $genre }}</span>
                    @endforeach
                </div>
            @endif
            @if ($shortDescription)
                <p class="game-detail-description">{{ $shortDescription }}</p>
            @endif
            @if (! empty($developers))
                <p class="game-detail-developer">Desarrollado por <strong>{{ implode(', ', $developers) }}</strong></p>
            @endif
        </div>
    @endif

    <div class="game-detail-body">
        @if ($steamRatingPercent > 0 || $metacriticScore > 0 || $releaseDate || $publisher)
            <div class="game-detail-meta">
                @if ($steamRatingPercent > 0)
                    <div class="rating-card {{ $steamRatingClass }}">
                        <div class="rating-label">Steam</div>
                        <div class="rating-score">{{ $steamRatingPercent }}%</div>
                        @if ($steamRatingText)
                            <div class="rating-text">{{ $steamRatingText }}</div>
                        @endif
                        @if ($steamRatingCount > 0)
                            <div class="rating-count">{{ number_format($steamRatingCount) }} reseñas</div>
                        @endif
                    </div>
                @endif
                @if ($metacriticScore > 0)
                    <div class="rating-card {{ $metacriticClass }}">
                        <div class="rating-label">Metacritic</div>
                        <div class="rating-score">{{ $metacriticScore }}</div>
                        @if ($metacriticLink)
                            <a class="rating-text" href="https://www.metacritic.com{{ $metacriticLink }}" target="_blank" rel="noopener">Ver reseña →</a>
                        @endif
                    </div>
                @endif
                @if ($publisher || $releaseDate)
                    <div class="rating-card">
                        @if ($publisher)
                            <div class="rating-label">Distribuidor</div>
                            <div class="rating-text">{{ $publisher }}</div>
                        @endif
                        @if ($releaseDate)
                            <div class="rating-label" style="margin-top:.5rem;">Lanzamiento</div>
                            <div class="rating-text">{{ $releaseDate }}</div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

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

            @auth
                <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                    @csrf
                    <input type="hidden" name="deal_id" value="{{ $dealID }}">
                    <button type="submit" class="auth-submit-btn">
                        <i class="bi bi-cart-plus"></i> Añadir al carrito
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="auth-submit-btn mt-3 d-inline-block text-center">
                    <i class="bi bi-box-arrow-in-right"></i> Inicia sesión para comprar
                </a>
            @endauth
        </div>

        @if (! empty($deal['cheaperStores']))
            <div class="cheaper-stores">
                <h3 class="section-title">Otras tiendas con precios similares</h3>
                <ul class="store-list">
                    @foreach (array_slice($deal['cheaperStores'], 0, 5) as $store)
                        <li>Tienda #{{ $store['storeID'] }} — <strong>${{ $store['salePrice'] }}</strong> <span class="old-price">${{ $store['retailPrice'] }}</span></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection
