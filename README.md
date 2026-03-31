# Portfolio & Admin Dashboard

## Project Overview
Full-stack portfolio application with two sides:
- Public site: dynamic bio, project grid, and contact form
- Admin dashboard: login-protected overview, project CRUD, inbox, profile/bio updates, and password tools

## Feature List
- Public bio loaded from backend
- Public projects loaded from backend
- Contact form submission to backend JSON store
- JWT-based admin authentication
- Admin overview (total projects and messages)
- Admin project create, read, update, delete
- Admin inbox listing
- Admin profile update
- Admin bio update
- Change password and reset password

## Tech Stack
| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript (ES6+), `fetch()` |
| Backend | PHP 8.1+, Composer, `firebase/php-jwt` |
| Data Store | JSON flat files |

## Project Structure
```text
portfolio-admin/
├── backend/
│   ├── api/
│   │   ├── public/
│   │   └── admin/
│   ├── config/
│   ├── data/
│   ├── middleware/
│   ├── utils/
│   ├── .htaccess
│   ├── .env
│   ├── .env.example
│   ├── .gitignore
│   ├── composer.json
│   └── README.md
├── frontend/
│   ├── index.html
│   ├── admin/
│   ├── css/
│   └── js/
└── README.md
```

## Quick Start
1. Backend setup:
	- `cd backend`
	- `composer install`
	- Verify `.env` values
2. Start backend server:
	- `php -S localhost:5000 -t backend/`
3. Frontend:
	- Open `frontend/index.html` directly in browser, or
	- Serve `frontend/` with a local server (such as Live Server)

## Environment Variables
Defined in `backend/.env`:
- `JWT_SECRET`
- `ADMIN_USERNAME`
- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`
- `ADMIN_PROFILE_IMAGE`
- `FRONTEND_URL`

## Authentication Flow
1. Open `frontend/admin/login.html`.
2. Submit email/password to `/api/admin/login.php`.
3. Receive JWT token and save to `localStorage` as `auth_token`.
4. Admin pages include token in `Authorization` header.
5. Backend validates token before protected actions.
6. On `401`, frontend clears token and redirects to login.

## Production Deployment Checklist
- Change `JWT_SECRET` to a strong random string.
- Update `FRONTEND_URL` to your production domain.
- Set CORS in `.htaccess` to production domain only.
- Set `display_errors = Off` in PHP.
- Ensure `/data` directory is not web-accessible.
- Use HTTPS.
- Change admin password immediately after deploy.
