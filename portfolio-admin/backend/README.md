# Backend

## Overview
This backend powers a portfolio public site and a protected admin dashboard. It is built with PHP 8.1+, JSON flat files, and JWT authentication using `firebase/php-jwt`.

## Requirements
- PHP 8.1 or newer
- Composer

## Setup
1. Open a terminal in `backend/`.
2. Install dependencies:
	 `composer install`
3. Copy `.env.example` to `.env` if needed and update values.
4. Ensure the `data/` directory is writable by PHP.

## Run
Use the PHP built-in server from the project root:

```bash
php -S localhost:5000 -t backend/
```

## API Reference
| Method | Path | Auth | Request Body | Response |
|---|---|---|---|---|
| GET | `/api/public/bio.php` | No | None | `{ content, updated_at }` |
| GET | `/api/public/projects.php` | No | None | `[{ id, project_name, title, description, image_url, created_at }]` |
| POST | `/api/public/messages.php` | No | `{ name, email, message }` | `201 { message: "Message received." }` |
| POST | `/api/admin/login.php` | No | `{ email, password }` | `{ token, admin: { id, username, email, profile_image } }` |
| GET | `/api/admin/me.php` | Yes | None | `{ id, username, email, profile_image }` |
| PUT | `/api/admin/profile.php` | Yes | `{ username, email, profile_image }` | Updated admin object |
| GET | `/api/admin/bio.php` | Yes | None | `{ content, updated_at }` |
| PUT | `/api/admin/bio.php` | Yes | `{ content }` | Updated bio object |
| PUT | `/api/admin/password.php` | Yes | `{ current_password, new_password, confirm_password }` | `{ message }` |
| PUT | `/api/admin/password_reset.php` | Yes | `{ new_password, confirm_password }` | `{ message }` |
| GET | `/api/admin/projects.php` | Yes | None | Projects array (newest first) |
| POST | `/api/admin/projects.php` | Yes | `{ project_name, title, description, image_url }` | `201` New project object |
| PUT | `/api/admin/projects.php?id=...` | Yes | `{ project_name, title, description, image_url }` | Updated project object |
| DELETE | `/api/admin/projects.php?id=...` | Yes | None | `{ message: "Project deleted." }` |
| GET | `/api/admin/messages.php` | Yes | None | Messages array (newest first) |

## Authentication (JWT Flow)
1. Client posts admin credentials to `/api/admin/login.php`.
2. Backend verifies password with `password_verify`.
3. On success, backend returns a signed JWT.
4. Client stores token and sends it as `Authorization: Bearer <token>`.
5. Protected endpoints validate token in `middleware/auth.php`.

## Data Storage
Data is stored as JSON files in `backend/data/`.

- `admin.json`
	- Single object
	- Contains admin profile and `password_hash`
- `bio.json`
	- Single object: `content`, `updated_at`
- `projects.json`
	- Array of project objects
- `messages.json`
	- Array of message objects

Writes use `LOCK_EX` through `write_json()` to reduce race conditions.

## Security Notes
- Passwords are hashed with `password_hash(PASSWORD_BCRYPT)`.
- Password hashes are never returned in API responses.
- Admin routes require JWT except login.
- CORS is restricted to `FRONTEND_URL`.
- `.htaccess` blocks direct access to `/data`.
- Login endpoint includes file-based rate limiting state in `admin.json`.
- Errors returned to clients are generic (`Server error`) to avoid leaking internals.
