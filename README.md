# Legacy Data API

A zero-framework PHP JSON API built from scratch. It demonstrates how to ingest, clean, and serve **legacy CSV data** over HTTP using only core PHP patterns — no Laravel, no Symfony, no magic.

Built to prove that real backend architecture doesn't require a framework to be clean.

---

## What It Does

- Reads user records from a CSV dataset and returns them as a structured JSON API
- Accepts new user entries via `POST` and persists them to the CSV
- Accepts bulk CSV file uploads via multipart form-data
- All responses are consistently `application/json` — no mixed HTML/text output

---

## Architecture

```
public/index.php       ← Single entry point (Front Controller)
core/
  Router.php           ← HTTP router with static + dynamic {param} matching
  Request.php          ← Abstracts method, path, body, and file uploads
app/
  Controllers/
    UsersController.php
  models/
    Model.php          ← Base model (lazy PDO singleton)
    Users.php          ← CSV-backed data access
config/
  Database.php         ← PDO connection via environment variables
routes/
  web.php              ← Route definitions
storage/
  data/
    users.csv          ← Clean dataset
    messy_users.csv    ← Raw legacy data (intentionally dirty)
  uploads/             ← Imported CSV files land here
```

---

## API Reference

| Method | Endpoint        | Description                        |
|--------|-----------------|------------------------------------|
| GET    | `/users`        | Return all users as a JSON array   |
| GET    | `/users/{id}`   | Return a single user by ID         |
| POST   | `/users`        | Add a new user (name, email)       |
| POST   | `/users/import` | Upload a CSV file of users         |

### GET `/users`
```json
{
  "data": [
    { "id": "1", "name": "John Doe", "email": "john@example.com" }
  ],
  "count": 4
}
```

### GET `/users/1`
```json
{
  "data": { "id": "1", "name": "John Doe", "email": "john@example.com" }
}
```

### POST `/users`
**Body** (JSON or `application/x-www-form-urlencoded`):
```json
{ "name": "Amara Osei", "email": "amara@example.com" }
```
**Response** `201 Created`:
```json
{ "message": "User added successfully", "data": { "name": "Amara Osei", "email": "amara@example.com" } }
```

### POST `/users/import`
**Body**: `multipart/form-data` with a field named `usersFile` containing a `.csv` file.

**Response** `201 Created`:
```json
{ "message": "File imported successfully", "filename": "my_users_20260514.csv" }
```

---

## Getting Started

### Requirements
- PHP 8.1+
- Composer

### Installation

```bash
git clone https://github.com/your-username/legacy-data-api.git
cd legacy-data-api
composer install
cp .env.example .env
```

### Running Locally

```bash
cd public
php -S localhost:8000
```

Then open Postman or curl and hit `http://localhost:8000/users`.

---

## Key Concepts Demonstrated

| Concept | Implementation |
|---|---|
| Front Controller Pattern | `public/index.php` routes every request |
| Custom HTTP Router | Static and `{param}` placeholder matching |
| PSR-4 Autoloading | Composer — no manual `require` chains |
| Request Abstraction | Unified access to GET, POST, JSON, and file payloads |
| CSV as a Data Source | Reading legacy flat-file data and normalising headers |
| File Upload Handling | `$_FILES` validation, MIME check, safe naming |
| Environment Variables | `.env` via `vlucas/phpdotenv` |
| JSON-only Responses | Every response — including errors — is `application/json` |

---

## Error Responses

All errors follow a consistent shape:

```json
{ "error": "User with ID 99 not found" }
```

HTTP status codes are used semantically: `400`, `404`, `422`, `500`.

---

## License

MIT
