# RaahSanj API

A Laravel 10 API for managing categorized questionnaire questions and reusable 1–5 response scales.

## Setup

1. Copy `.env.example` to `.env` and configure `DB_*` plus a strong `ADMIN_PASSWORD`.
2. Install dependencies: `composer install`.
3. Generate an app key: `php artisan key:generate`.
4. Create the schema and demo data: `php artisan migrate --seed`.
5. Start the API: `php artisan serve`.

The seeder creates the administrator configured through `ADMIN_NAME`, `ADMIN_EMAIL`, and `ADMIN_PASSWORD`. Change these values before running the seeder in any non-local environment.

## Authentication

All `/api/v1/admin/*` management endpoints require an administrator Bearer token.

```http
POST /api/v1/admin/login
Content-Type: application/json

{"email":"admin@example.com","password":"your-password","device_name":"admin-panel"}
```

Use the returned token on admin requests:

```http
Authorization: Bearer <token>
```

`POST /api/v1/admin/logout` revokes the token used for that request.

## Public endpoints

| Endpoint | Description |
| --- | --- |
| `GET /api/v1/questions` | Active questions across all active categories. |
| `GET /api/v1/questions/{category-slug}` | Active questions in one active category. |

Public responses intentionally exclude `correct_answer`; answer keys are available only via authenticated admin endpoints.

## Tests

Run `php artisan test`. The feature tests use an in-memory SQLite database and cover public response safety plus administrator login and access control.
