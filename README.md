# Shopping_App — Order & Inventory Mini-System

This app implements a simple retail counter for orders and inventory.

Setup:

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan queue:work &
```

API endpoints (basic):

- POST /api/orders — create order
- GET /api/orders/history?email=... — customer orders
- GET /api/products/low-stock?threshold=5 — low stock products
