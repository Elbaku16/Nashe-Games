<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nashe Games - @yield('title')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS propio --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="app-container">

        {{-- SIDEBAR --}}
        <aside class="sidebar">
            <div class="sidebar-top">
                @auth
                    <a href="{{ route('store') }}" class="nav-item {{ request()->routeIs('store*') ? 'active' : '' }}">
                        <i class="bi bi-tag-fill"></i>
                        <span>Store</span>
                    </a>
                    <a href="{{ route('library') }}" class="nav-item {{ request()->routeIs('library') ? 'active' : '' }}">
                        <i class="bi bi-collection-fill"></i>
                        <span>Library</span>
                    </a>
                    <a href="{{ route('cart') }}" class="nav-item {{ request()->routeIs('cart*') ? 'active' : '' }}">
                        <i class="bi bi-cart-fill"></i>
                        <span>Cart</span>
                        @if (($cartCount = auth()->user()->cartItems()->count()) > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                @endauth
            </div>

            <div class="sidebar-bottom">
                @auth
                    <div class="user-info">
                        <i class="bi bi-person-circle"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="user-info {{ request()->routeIs('login') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i>
                        <span>Guest</span>
                    </a>
                @endauth
            </div>
        </aside>

        {{-- CONTENIDO PRINCIPAL --}}
        <main class="main-content">
            @if (session('status'))
                <div class="flash-message">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
