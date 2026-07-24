# ✈️ Airline Voucher Seat Assignment

A full-stack web application that lets airline crew generate exactly **three unique random voucher seats** for a flight, based on aircraft-specific seat layouts (ATR, Airbus 320, Boeing 737 Max).

## Tech Stack

| Layer    | Technology                                                                 |
|----------|-----------------------------------------------------------------------------|
| Frontend | React 18, Vite, Axios, Tailwind CSS                                        |
| Backend  | PHP 8.3, Laravel 11, SQLite, Eloquent ORM, Form Requests, API Resources     |

## Project Structure

```
airline-voucher/
├── backend/                 # Laravel 11 API
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/V1/VoucherController.php
│   │   │   ├── Requests/CheckVoucherRequest.php
│   │   │   ├── Requests/GenerateVoucherRequest.php
│   │   │   └── Resources/VoucherResource.php
│   │   ├── Models/Voucher.php
│   │   ├── Services/SeatGeneratorService.php   # seat-map + randomization logic
│   │   ├── Services/VoucherService.php         # orchestration + persistence
│   │   └── Exceptions/VoucherAlreadyExistsException.php
│   ├── database/
│   │   ├── migrations/          # vouchers table + framework tables
│   │   ├── factories/VoucherFactory.php
│   │   └── seeders/VoucherSeeder.php
│   ├── routes/api.php
│   ├── tests/Feature/VoucherTest.php
│   ├── tests/Unit/SeatGeneratorServiceTest.php
│   ├── openapi.yaml             # Swagger / OpenAPI documentation
│   └── Dockerfile
├── frontend/                # React 18 + Vite
│   ├── src/
│   │   ├── api/axios.js
│   │   ├── components/VoucherForm.jsx
│   │   ├── components/SeatResult.jsx
│   │   ├── components/Notification.jsx
│   │   └── App.jsx
│   └── Dockerfile
├── docker-compose.yml
└── README.md
```

## Architecture & Design Decisions

- **Service Layer**: `SeatGeneratorService` owns aircraft seat-map knowledge and pure random-seat generation (Single Responsibility). `VoucherService` orchestrates existence checks, persistence, and logging, and is the only class that touches the `Voucher` model — controllers stay thin.
- **Form Requests**: `CheckVoucherRequest` and `GenerateVoucherRequest` validate input (including that `aircraft` is one of the three supported types) before it reaches the controller.
- **API Resource**: `VoucherResource` shapes the JSON response for the `generate` endpoint.
- **Race-condition safety**: `VoucherService::generate()` wraps the existence check + insert in a DB transaction with `lockForUpdate()`, and the `vouchers` table has a **unique index** on `(flight_number, flight_date)` as a hard database-level guarantee.
- **Exception Handling**: A dedicated `VoucherAlreadyExistsException` is mapped to a `409 Conflict` JSON response in `bootstrap/app.php`; validation errors return `422`, all other API errors return sanitized JSON instead of leaking a stack trace.
- **API Versioning**: Canonical routes are under `/api/v1/...`; unversioned aliases (`/api/check`, `/api/generate`) are also exposed to match the task's exact contract.
- **Dependency Injection**: `VoucherService` and `SeatGeneratorService` are constructor-injected everywhere (controller, form request), resolved automatically by Laravel's container.

## Aircraft Seat Layouts

| Aircraft         | Rows  | Columns          |
|-------------------|-------|------------------|
| ATR               | 1–18  | A, C, D, F       |
| Airbus 320        | 1–32  | A, B, C, D, E, F |
| Boeing 737 Max    | 1–32  | A, B, C, D, E, F |

## API Reference

Base URL: `http://localhost:8000/api` (also available under `/api/v1`)

### `POST /api/check`

**Request**
```json
{ "flightNumber": "GA102", "date": "2025-07-12" }
```
**Response** `200`
```json
{ "exists": true }
```

### `POST /api/generate`

**Request**
```json
{
  "name": "Sarah",
  "id": "98123",
  "flightNumber": "GA102",
  "date": "2025-07-12",
  "aircraft": "Airbus 320"
}
```
**Response** `201`
```json
{ "success": true, "seats": ["3B", "7C", "14D"] }
```
**Response** `409` (already generated)
```json
{
  "success": false,
  "exists": true,
  "message": "Vouchers have already been generated for flight GA102 on 2025-07-12."
}
```

Full OpenAPI/Swagger spec: [`backend/openapi.yaml`](./backend/openapi.yaml) — paste it into [editor.swagger.io](https://editor.swagger.io) to explore interactively.

## 🪟 Catatan untuk Windows

Semua contoh command di README ini pakai syntax **bash/Unix** (`cp`, `touch`, dll). Kalau kamu menjalankan dari **Terminal Laragon** atau **Git Bash**, command ini langsung jalan tanpa modifikasi.

Kalau kamu pakai **PowerShell** (default terminal VS Code / Windows Terminal), ganti perintahnya sebagai berikut:

| Perintah di README      | Ganti dengan (PowerShell)                          |
|--------------------------|-----------------------------------------------------|
| `cp .env.example .env`   | `Copy-Item .env.example .env`                       |
| `touch database/database.sqlite` | `New-Item -ItemType File -Force -Path database\database.sqlite` |

Kalau pakai **Command Prompt (cmd.exe)**:

| Perintah di README      | Ganti dengan (CMD)                                  |
|--------------------------|-----------------------------------------------------|
| `cp .env.example .env`   | `copy .env.example .env`                            |
| `touch database/database.sqlite` | `type nul > database\database.sqlite`              |

## 🧰 Cara Install

### Prasyarat
- PHP 8.3+ dan Composer
- Node.js 18+ dan npm
- Ekstensi SQLite3 aktif di PHP (`pdo_sqlite`)

### 1. Clone repository

```bash
git clone https://github.com/<username>/<nama-repo>.git
cd <nama-repo>
```

### 2. Install dependency Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Install dependency Frontend (React)

```bash
cd ../frontend
npm install
cp .env.example .env
```

## 🗄️ Migration (Database)

Dijalankan dari folder `backend`:

```bash
cd backend
touch database/database.sqlite    # buat file SQLite kosong
php artisan migrate               # membuat tabel, termasuk "vouchers"
php artisan db:seed               # opsional: isi data contoh
```

> 💻 Pengguna Windows: ganti `touch database/database.sqlite` sesuai tabel di [Catatan untuk Windows](#-catatan-untuk-windows) di atas.

Perintah `migrate` akan membuat tabel `vouchers` beserta unique index gabungan `flight_number + flight_date`, sesuai requirement.

## ▶️ Menjalankan Laravel (Backend)

```bash
cd backend
php artisan serve
```
Backend berjalan di **http://localhost:8000**, dengan endpoint:
- `POST http://localhost:8000/api/check`
- `POST http://localhost:8000/api/generate`

## ▶️ Menjalankan React (Frontend)

Buka terminal baru:
```bash
cd frontend
npm run dev
```
Frontend berjalan di **http://localhost:5173** dan otomatis memanggil backend di `http://localhost:8000/api` (bisa diubah lewat `VITE_API_BASE_URL` di file `.env`).

> Jalankan backend dan frontend **secara bersamaan** (2 terminal terpisah) agar aplikasi berfungsi penuh.

### Run tests

```bash
cd backend
php artisan test
# or
./vendor/bin/phpunit
```

## Getting Started (Docker)

```bash
docker compose up --build
```
- Backend API → http://localhost:8000
- Frontend → http://localhost:5173

The backend container automatically runs `composer install`, generates the app key, creates the SQLite database, and migrates on build.

## User Flow

```
Fill Form → POST /api/check
                │
        ┌───────┴────────┐
    exists = true     exists = false
        │                  │
   Show Error       POST /api/generate
                            │
                    Display 3 Seats
```

## Troubleshooting

- **Error `The bootstrap/cache directory must be present and writable`** saat `composer install`: folder kosong (`bootstrap/cache`, `storage/framework/*`, `storage/logs`) memang perlu ada sebelum Laravel bisa jalan. Folder ini sudah disertakan lewat file `.gitkeep` di repo ini — kalau tetap terjadi, buat manual folder tersebut lalu ulangi `composer install`.
- **Error `could not find driver` / migration gagal**: pastikan extension `pdo_sqlite` dan `sqlite3` aktif di `php.ini` (uncomment baris `extension=pdo_sqlite` dan `extension=sqlite3`), lalu restart PHP/Laragon.
- **Frontend tidak bisa connect ke backend (CORS error)**: pastikan `CORS_ALLOWED_ORIGINS` di `backend/.env` mencantumkan origin frontend kamu (default `http://localhost:5173`).

## Notes

- The `.env` file is intentionally excluded from version control; `.env.example` is provided for both backend and frontend.
