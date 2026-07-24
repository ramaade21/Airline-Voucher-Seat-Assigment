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

## 🪟 Quick Start untuk Windows (Laragon)

Untuk testing cepat di **Windows 10 + Laragon**, gunakan script yang sudah disediakan di root project (jalankan lewat **double-click** atau lewat Terminal Laragon):

| Script       | Fungsi                                                                 |
|--------------|-------------------------------------------------------------------------|
| `setup.bat`  | Cek PHP/Composer/Node, install dependency backend & frontend, buat `.env`, generate key, buat `database.sqlite`, jalankan migration, tanya mau seed atau tidak |
| `start.bat`  | Menjalankan `php artisan serve` dan `npm run dev` masing-masing di jendela CMD terpisah, lalu otomatis membuka browser ke `http://localhost:5173` |
| `stop.bat`   | Menghentikan proses yang berjalan di port 8000 dan 5173                 |

**Langkah:**
1. Pastikan Laragon sudah terpasang, PHP 8.3+ aktif, extension `pdo_sqlite`/`sqlite3` sudah di-uncomment di `php.ini`, dan Node.js sudah ter-install (lihat detail di bawah).
2. Extract project, lalu double-click `setup.bat` (sekali saja).
3. Setelah selesai, double-click `start.bat` setiap kali mau menjalankan aplikasi.
4. Double-click `stop.bat` untuk menghentikan server.

> Jika `setup.bat`/`start.bat` gagal dijalankan langsung dari File Explorer, buka **Terminal Laragon**, `cd` ke folder project, lalu jalankan `setup.bat` / `start.bat` dari situ — ini menghindari masalah PATH pada CMD default Windows.

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

## Notes

- This sandbox environment could not reach `packagist.org`, so `vendor/` is not pre-installed — run `composer install` per the instructions above to fetch Laravel framework dependencies.
- The `.env` file is intentionally excluded from version control; `.env.example` is provided for both backend and frontend.
