# Bakery Management System (PHP + MySQL + Tailwind)
This is a starter scaffold for a Bakery Management System using:
- PHP (PDO)
- MySQL (schema provided)
- Tailwind CSS (CDN)
- Simple role-based auth (sessions)

## Included
- `public/` : web root (index, assets)
- `app/` : controllers, models, views scaffold
- `config/` : config.php (PDO) and .env.example
- `sql/schema.sql` : database schema dump to import via phpMyAdmin
- Basic modules: auth, employees, raw_materials, products, production, sales
- `composer.json` : minimal for future packages

## Setup
1. Copy files to your server.
2. Create a MySQL database and import `sql/schema.sql`.
3. Copy `.env.example` to `.env` and set DB credentials.
4. Run `composer install` if you add packages.
5. Point your webserver to `public/` folder.

