# Shopping_App - Order & Inventory Mini-System

This app implements a simple retail counter for orders and inventory.

## Setup

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan queue:work &
```

## API Endpoints

Base URL:

```bash
http://127.0.0.1:8000/api
```

### 1) Create an order

```http
POST /api/orders
Content-Type: application/json
```

Request body:

```json
{
  "customer": {
    "name": "Alice Johnson",
    "email": "alice@example.com"
  },
  "items": [
    {
      "product_id": 1,
      "quantity": 2
    },
    {
      "product_id": 3,
      "quantity": 1
    }
  ],
  "amount_paid": 250.00
}
```

Response:

```json
{
  "success": true,
  "order": {
    "id": 1,
    "customer": {
      "name": "Alice Johnson",
      "email": "alice@example.com"
    },
    "items": []
  }
}
```

### 2) Check stock availability

```http
POST /api/orders/check-stock
Content-Type: application/json
```

Request body:

```json
{
  "product_id": 1,
  "quantity": 3
}
```

Response:

```json
{
  "success": true,
  "available": true,
  "product": "Product Name",
  "requested": 3,
  "available_stock": 10,
  "message": "Stock available."
}
```

### 3) Get order history for a customer

```http
GET /api/orders/history?email=alice@example.com
```

Response:

```json
{
  "success": true,
  "customer": {
    "id": 1,
    "name": "Alice Johnson",
    "email": "alice@example.com"
  },
  "orders": []
}
```

### 4) Get a single order by ID

```http
GET /api/orders/{order}
```

Example:

```http
GET /api/orders/1
```

### 5) Get all customers summary

```http
GET /api/orders/customers
```

Response:

```json
{
  "success": true,
  "customers": [
    {
      "id": 1,
      "name": "Alice Johnson",
      "email": "alice@example.com",
      "orders_count": 2,
      "total_spent": 250.5
    }
  ]
}
```

### 6) Get low-stock products

```http
GET /api/products/low-stock?threshold=5
```

Example response:

```json
{
  "success": true,
  "threshold": 5,
  "products": [
    {
      "id": 2,
      "name": "Keyboard",
      "stock_on_hand": 3
    }
  ]
}
```

## Sending JSON in Postman or curl

### curl example

```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "customer": {
      "name": "Alice Johnson",
      "email": "alice@example.com"
    },
    "items": [
      {"product_id": 1, "quantity": 2}
    ],
    "amount_paid": 220.00
  }'
```

### Postman

1. Set method to `POST`
2. Enter URL: `http://127.0.0.1:8000/api/orders`
3. Select `Body` → `raw`
4. Choose `JSON`
5. Paste the JSON body shown above

## Notes

- The API expects JSON request bodies for POST endpoints.
- Validation errors return JSON with a `success: false` response and HTTP 422 status.
- The route prefix is `/api`, so do not use `/orders/...` without `/api` when calling the JSON API.
