# Nashe Games — Setup

Tienda de videojuegos hecha con **Laravel 13**, con datos en vivo desde la API pública de [CheapShark](https://apidocs.cheapshark.com/).

## Stack

- PHP 8.3+, Laravel 13
- SQLite (por defecto)
- Bootstrap 5 + CSS propio
- Autenticación nativa de Laravel (sesión)
- API: CheapShark (sin API key, sin rate limit ajustado)

## Setup (primera vez)

```bash
# 1. Instalar dependencias PHP
composer install

# 2. Instalar dependencias JS (opcional, Tailwind/Vite están instalados pero NO se usan)
npm install

# 3. Crear archivo de entorno
cp .env.example .env

# 4. Generar APP_KEY
php artisan key:generate

# 5. Crear la base de datos SQLite
touch database/database.sqlite

# 6. Correr migraciones (crea users, cart_items, library_items, cache, jobs, sessions)
php artisan migrate

# 7. Arrancar el servidor de desarrollo
php artisan serve
```

Abre `http://127.0.0.1:8000` y deberías ver el login.

## Flujo de uso

1. **Registrarse** en `/register` (campos: name, email, password, password confirmation — mínimo 6 caracteres).
2. Después del registro, te loguea y redirige a **/store**.
3. La **tienda** carga ofertas en vivo de CheapShark (steamStoreID=1, sortBy=Deal Rating):
   - 1 juego destacado en el hero (la mejor oferta del momento).
   - Grid con 12 cards de juegos en oferta.
   - Buscador: hace query `?title=...` a CheapShark.
4. Hacer click en **Add to cart** crea un `CartItem` en la BD con un snapshot del precio.
5. En **/cart** ves los items con checkboxes (todos seleccionados por defecto). Puedes destildar para excluirlos del pago, o eliminar con el botón papelera.
6. Al pagar (**Pay Now**), los items seleccionados se mueven a `library_items` (con la fecha de compra y precio histórico) y se borran del carrito.
7. En **/library** ves tus juegos comprados, con buscador y fecha de compra.

## Arquitectura

```
app/
├── Services/
│   └── CheapSharkService.php   # Cliente HTTP cacheado (10 min) para CheapShark
├── Models/
│   ├── User.php                # +relaciones cartItems(), libraryItems()
│   ├── CartItem.php
│   └── LibraryItem.php
└── Http/Controllers/
    ├── AuthController.php      # showLogin, showRegister, login, register, logout
    ├── StoreController.php     # index (con search), show (detalle de juego)
    ├── LibraryController.php
    └── CartController.php      # index, add, remove, checkout

database/migrations/
├── 2026_05_19_100001_create_cart_items_table.php
└── 2026_05_19_100002_create_library_items_table.php

resources/views/
├── layouts/
│   └── app.blade.php           # Sidebar con auth/guest, flash messages, cart badge
└── pages/
    ├── login.blade.php
    ├── register.blade.php
    ├── store.blade.php         # Featured + grid dinámicos
    ├── library.blade.php
    ├── cart.blade.php
    └── game.blade.php          # Detalle de un juego (precio histórico, cheaper stores)

routes/web.php                  # 12 rutas (4 guest + 8 auth)
public/css/app.css              # Estilos extendidos para datos dinámicos
```

## Endpoints de CheapShark usados

| Endpoint                                | Uso                                     |
|-----------------------------------------|-----------------------------------------|
| `GET /api/1.0/deals?storeID=1&pageSize=N&sortBy=Deal Rating&AAA=1` | Lista de ofertas para la tienda |
| `GET /api/1.0/deals?title=X`            | Búsqueda de juegos por título           |
| `GET /api/1.0/deals?id={dealID}`        | Detalle de una oferta + cheaper stores  |

Todas las respuestas se cachean 10 minutos para no abusar de la API.

## Notas

- Si CheapShark tarda o falla, el servicio retorna `[]` (la tienda quedará vacía pero no rompe la app).
- El carrito está limitado a 1 instancia por `(user_id, deal_id)` (índice único).
- El carrito y la biblioteca guardan **snapshots** del precio al momento de la acción — si el deal cambia después, lo comprado mantiene el precio histórico.
- El logout es POST con CSRF (estándar Laravel) — el botón del sidebar lo envía vía formulario.
- Las únicas dependencias **nuevas** vs el repo original son cero — todo se hace con lo que Laravel 13 ya trae (`Illuminate\Http\Client`, `Illuminate\Support\Facades\Cache`, etc.).

## Credenciales de prueba

No hay seeders de usuarios. Crea el tuyo en `/register`.
