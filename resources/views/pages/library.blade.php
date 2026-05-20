@extends('layouts.app')

@section('title', 'Library')

@section('content')
<div class="top-bar">
    <h1 class="page-title">Library</h1>
    <form action="{{ route('library') }}" method="GET" class="search-form">
        <input type="text" name="search" class="search-bar" placeholder="Search..." value="{{ $search }}">
    </form>
</div>

<div class="tabs">
    <span class="tab active">Owned games ({{ $items->count() }})</span>
</div>

@if ($items->isEmpty())
    <div class="empty-state">
        <i class="bi bi-collection"></i>
        <p>
            @if ($search !== '')
                Sin coincidencias para "{{ $search }}".
            @else
                Tu biblioteca está vacía. Visita la <a href="{{ route('store') }}">tienda</a> para comprar juegos.
            @endif
        </p>
    </div>
@else
    <div class="games-grid">
        @foreach ($items as $item)
            <div>
                <div class="game-card"
                     style="background-image: linear-gradient(180deg, rgba(0,0,0,0) 50%, rgba(0,0,0,0.85) 100%), url('{{ $item->thumb }}');">
                    <span class="game-card-label">{{ $item->title }}</span>
                </div>
                <div class="game-info">
                    <span>{{ $item->title }}</span>
                    <button class="play-btn" title="Play"><i class="bi bi-play-fill"></i></button>
                </div>
                <div class="game-info">
                    <span class="purchase-date">
                        Comprado: {{ optional($item->purchased_at)->format('d/m/Y') ?? '—' }}
                    </span>
                    <span class="dots">···</span>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
