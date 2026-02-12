# Event Booking System (Backend)

Laravel 12 API-only backend for event booking with Sanctum authentication, RBAC, events/tickets/bookings/payments, caching, and queued notifications.

## Requirements
- PHP 8.2+
- Composer
- Laravel 12
- Database: MySQL (default) or SQLite

## Setup
1. `composer install`
2. Copy env file locally: `cp .env.example .env`
3. Configure DB connection in `.env` and generate app key:
   - `php artisan key:generate`
4. Run migrations and seeders:
   - `php artisan migrate:fresh --seed`
5. Run tests:
   - `php artisan test`
6. Start server:
   - `php artisan serve`

## Important Notes
- All endpoints are versioned under `/api/v1`.
- No unversioned API routes exist.
- Roles are stored as string values: `admin`, `organizer`, `customer`.
- `tickets.quantity` is remaining inventory (decremented on successful payment).
- Payment flow is mocked using `force_success` (boolean, default `true`).
- `/api/v1/bookings` is forbidden for organizer in current scope.
- `PreventDoubleBooking` blocks same user + same ticket when booking is `pending` or `confirmed`.
- Events list cache:
  - only cached when query params contain only `page`
  - TTL is 120 seconds
  - cache key uses version invalidation (`events:index:version`)
- Payment idempotency:
  - unique `payments.booking_id`
  - duplicate payment returns `409 Conflict`

## API Endpoints (/api/v1)

| Module | Method | Endpoint |
|---|---|---|
| Ping | GET | `/api/v1/ping` |
| Auth | POST | `/api/v1/auth/register` |
| Auth | POST | `/api/v1/auth/login` |
| Auth | POST | `/api/v1/auth/logout` |
| User | GET | `/api/v1/user/me` |
| Events | GET | `/api/v1/events` |
| Events | GET | `/api/v1/events/{id}` |
| Events | POST | `/api/v1/events` |
| Events | PUT | `/api/v1/events/{id}` |
| Events | DELETE | `/api/v1/events/{id}` |
| Tickets | POST | `/api/v1/events/{event_id}/tickets` |
| Tickets | PUT | `/api/v1/tickets/{id}` |
| Tickets | DELETE | `/api/v1/tickets/{id}` |
| Bookings | POST | `/api/v1/tickets/{id}/bookings` |
| Bookings | GET | `/api/v1/bookings` |
| Bookings | PUT | `/api/v1/bookings/{id}/cancel` |
| Payments | POST | `/api/v1/bookings/{id}/payment` |
| Payments | GET | `/api/v1/payments/{id}` |

## cURL Examples

### Register
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Customer",
    "email": "john.customer@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "0601234567"
  }'
```

### Login
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john.customer@example.com",
    "password": "password123"
  }'
```

### Me
```bash
curl -X GET http://127.0.0.1:8000/api/v1/user/me \
  -H "Authorization: Bearer <TOKEN>"
```

### Create Event (Organizer/Admin)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/events \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Tech Expo 2026",
    "description": "Annual expo",
    "date": "2026-09-10 10:00:00",
    "location": "Belgrade"
  }'
```

### Create Ticket (Organizer/Admin)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/events/1/tickets \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "type": "Standard",
    "price": 80.00,
    "quantity": 50
  }'
```

### Create Booking (Customer)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/tickets/1/bookings \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "quantity": 2
  }'
```

### Pay Booking (force_success=true)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/bookings/1/payment \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "force_success": true
  }'
```

### Pay Booking (force_success=false)
```bash
curl -X POST http://127.0.0.1:8000/api/v1/bookings/1/payment \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{
    "force_success": false
  }'
```

## Postman
- Collection file: `postman/EventBookingSystem.postman_collection.json`
- Variables:
  - `base_url` (default: `http://127.0.0.1:8000`)
  - `token` (Bearer token)
