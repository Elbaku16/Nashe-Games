@extends('layouts.app')

@section('title', 'Biblioteca')

@section('content')
<div class="top-bar">
    <h1 class="page-title">Biblioteca</h1>
    <form action="{{ route('library') }}" method="GET" class="search-form">
        <input type="text" name="search" class="search-bar" placeholder="Buscar..." value="{{ $search }}">
    </form>
</div>

<div class="tabs">
    <span class="tab active">Juegos adquiridos ({{ $items->count() }})</span>
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
                     style="background-image: linear-gradient(180deg, rgba(0,0,0,0) 50%, rgba(0,0,0,0.85) 100%), url('{{ $item->displayImage ?? $item->thumb }}');">
                    <span class="game-card-label">{{ $item->title }}</span>
                </div>
                <div class="game-info">
                    <span>{{ $item->title }}</span>
                    <button class="play-btn" title="Jugar"><i class="bi bi-play-fill"></i></button>
                </div>
                <div class="game-info">
                    <span class="purchase-date">
                        Comprado: {{ optional($item->purchased_at)->format('d/m/Y') ?? '—' }}
                    </span>
                    <div class="library-menu">
                        <button type="button" class="dots-btn" onclick="toggleLibraryMenu(this)" aria-haspopup="true">···</button>
                        <div class="library-menu-popup">
                            <form action="{{ route('library.uninstall', $item) }}" method="POST"
                                  onsubmit="return confirm('¿Seguro que deseas desinstalar &quot;{{ $item->title }}&quot;? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="library-menu-item danger">
                                    <i class="bi bi-trash"></i> Desinstalar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function toggleLibraryMenu(btn) {
            const popup = btn.nextElementSibling;
            const isOpen = popup.classList.contains('open');
            document.querySelectorAll('.library-menu-popup.open').forEach(el => el.classList.remove('open'));
            if (!isOpen) {
                popup.classList.add('open');
            }
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.library-menu')) {
                document.querySelectorAll('.library-menu-popup.open').forEach(el => el.classList.remove('open'));
            }
        });
    </script>
@endif
@endsection
