# Shana API

Backend API for **Shana**: SOS alerts, disaster and safe points, resources, and NGO user management. Built with [Laravel 12](https://laravel.com/docs/12.x) and [Laravel Sanctum](https://laravel.com/docs/sanctum) for token-based authentication.

## Requirements

- PHP **8.2+**
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) (for Vite / front-end asset tooling used by the default welcome page)

## Quick start

From the repository root:

```bash
composer run setup
```

This installs PHP dependencies, creates `.env` from `.env.example` if needed, generates `APP_KEY`, runs migrations, installs npm packages, and runs `npm run build`.

Then start the app:

```bash
php artisan serve
```

The API is served under the **`/api`** prefix (for example `http://127.0.0.1:8000/api/login`).

### Manual setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build
```

Configure your database in `.env` (defaults in `.env.example` use SQLite; set `DB_*` for MySQL or PostgreSQL if you prefer).

## Development

Run the app together with the queue worker, log tail, and Vite dev server:

```bash
composer run dev
```

Health check (no `/api` prefix): `GET /up`

## Tests

```bash
composer run test
```

## API overview

All routes below are relative to **`/api`**.

### Public

| Method | Path | Notes |
|--------|------|--------|
| Various | `/sos`, `/disaster`, `/points`, `/safe-points`, `/attachable` | REST resources (`apiResource`) |
| `GET` | `/approved-ngos` | Approved NGOs |
| `GET` | `/get-all-ngo` | NGO listing |
| `PATCH` | `/users/{user}/approve` | Approve user |
| `POST` | `/ngo/register` | NGO registration |
| `POST` | `/login` | Login |
| `POST` | `/ngo/login` | NGO login |

### Authenticated (`Authorization: Bearer <token>`, Sanctum)

| Method | Path | Notes |
|--------|------|--------|
| `GET` | `/dashboard/overview` | Dashboard overview |
| `GET` | `/me` | Current user |
| `POST` | `/ngo/logout` | Logout |
| `GET` | `/get-all-ngo` | NGO listing (also behind auth) |
| `PATCH` | `/users/{user}/approve` | Approve user |
| `DELETE` | `/resource/{resource}/delete` | Delete resource (safe points flow) |

Issue a token via `/login` or `/ngo/login` and send it on protected requests as a Bearer token.

## Project layout (high level)

- `routes/api.php` — API route definitions
- `app/Http/Controllers/` — Controllers (including `AuthController` and resource controllers)
- `database/migrations/` — Schema (users, SOS, disaster/safe points, attachables, resources, Sanctum tokens, etc.)

## License

This project follows the Laravel application skeleton; see `composer.json` for package licensing (MIT).
