# UTM Generator (Laravel + SingleStore) - Skeleton
This repository contains a minimal Laravel-ready skeleton for a UTM link generator app
using SingleStore (MySQL-compatible).

## What is included
- Migration: create_utm_links_table
- Model: UtmLink
- Controller: UtmController
- Routes (web.php)
- Blade view: resources/views/utm/form.blade.php
- .env.example and README instructions

## How to use
1. Place these files into a fresh Laravel 10+ project (copy into project root).
2. Update `.env` with your SingleStore credentials (DB_CONNECTION=mysql etc).
3. Run `composer install` (if not already), then `php artisan migrate`.
4. Serve: `php artisan serve` and visit `/utm`.

