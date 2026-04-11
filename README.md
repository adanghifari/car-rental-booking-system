# Car Rental Booking System

Laravel starter project for a car rental booking application.

## Stack

- Laravel 13
- Blade
- Vite
- Tailwind CSS 4
- PostgreSQL 18.3

## Requirements

- PHP 8.3+
- Composer
- Node.js 20+
- NPM
- PostgreSQL 18.3
- PHP extension `pdo_pgsql`

## Project Setup

1. Copy environment file:

```bash
cp .env.example .env
```

2. Install PHP and frontend dependencies:

```bash
composer run setup
```

3. Update database credentials in `.env` to match your local PostgreSQL 18.3 setup.

Default values in `.env.example`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=car_rental_booking
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

4. Create the database locally:

```bash
createdb car_rental_booking
```

5. Run database migrations:

```bash
php artisan migrate
```

6. Start the development environment:

```bash
composer run dev
```

The application will be available at `http://127.0.0.1:8000`.

## Database

Project ini menggunakan setup PostgreSQL manual, tanpa Docker.

Jika service PostgreSQL Anda berjalan di port atau user yang berbeda, sesuaikan nilai `DB_*` di `.env` sebelum menjalankan migrasi.

## Available Commands

```bash
composer run setup
composer run bootstrap
composer run dev
composer run lint
composer test
```

## Notes

- Keep shared defaults in `.env.example`.
- Do not commit `.env`, `vendor`, `node_modules`, or build artifacts.
- Use migrations for schema changes.
