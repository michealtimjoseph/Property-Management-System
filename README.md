# DreamHome — Property Management System

A web-based property management application built with **Laravel 12** and **Blade**, designed for real estate agencies to manage properties, staff, renters, viewings, lease agreements, inspections, and financial reporting.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Features](#features)
- [System Roles](#system-roles)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Project Structure](#project-structure)
- [Authentication](#authentication)
- [Key Modules](#key-modules)
- [PDF Reporting](#pdf-reporting)
- [Known Issues & Notes](#known-issues--notes)

---

## Tech Stack

| Layer       | Technology                                      |
|-------------|--------------------------------------------------|
| Backend     | PHP 8.2+, Laravel 12                             |
| Frontend    | Blade Templates, Tailwind CSS v3, Flowbite, Alpine.js |
| Charts      | ApexCharts                                       |
| Database    | PostgreSQL                                       |
| Auth        | Laravel Breeze (multi-guard: `web` + `staff`)    |
| PDF Export  | barryvdh/laravel-dompdf                          |
| Build Tool  | Vite + laravel-vite-plugin                       |

---

## Features

### Staff Portal (`/staff/*`)
- **Role-based Dashboard** — Regular staff see their own assignments; Managers see system-wide KPIs and a 7-day revenue chart
- **Property Management** — Create, edit, view, and assign properties
- **Renter & Owner Management** — Full CRUD for renters and property owners
- **Viewing Scheduling** — Schedule and assign viewings; submit feedback to mark as completed
- **Lease Management** — Create leases, process payments, manage renewals
- **Lease Applications** — Review, approve, or reject client-submitted applications
- **Property Listing Requests** — Approve or reject owner-submitted listing requests
- **Inspections** — Schedule and complete property inspections with evaluation notes
- **Reports** (Manager only) — Generate downloadable PDF reports:
  - Staff Productivity
  - Revenue Collection
  - Property Inventory
  - Inspection Logs
  - Payment History
- **Dashboard PDF Export** — Download a personalized operational/management report

### Client Portal (`/home`, `/leases`, `/viewings`, etc.)
- View active lease details and download lease PDF
- Request lease renewal or contact support
- Process advance payments
- Book property viewings
- Submit lease applications for listed properties
- Submit property listing requests (for owners)

---

## System Roles

| Role       | Guard   | Access                                              |
|------------|---------|-----------------------------------------------------|
| **Manager**| `staff` | Full system access including Reports module         |
| **Regular**| `staff` | Own assignments only (properties, viewings, leases) |
| **Client** | `web`   | Client portal only (leases, viewings, applications) |

Role-based access control is enforced inside controllers via `$staff->position` checks — not at the route level.

---

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 18 + npm
- PostgreSQL
- Git

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/michealtimjoseph/Property-Management-System.git
cd Property-Management-System

# 2. Install PHP dependencies
composer install

# 3. Install Laravel Breeze (dev dependency for auth scaffolding)
composer require laravel/breeze --dev
php artisan breeze:install

# 4. Install Node dependencies
npm install

# 5. Install Flowbite (UI component library)
npm install flowbite

# 6. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 7. Create the storage symlink
php artisan storage:link
```

---

## Database Setup

1. Create a PostgreSQL database (e.g., `dreamhome`).

2. Update your `.env` file:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=dreamhome
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

3. Run migrations:

```bash
php artisan migrate
```

> **Note:** The `staff` and `property` tables are expected to exist in PostgreSQL before migrations that reference them (foreign keys). If you are setting up from scratch and the `staff` table migration is empty (placeholder only), you will need to seed or manually create the full schema. See the Staff model for the expected columns.

4. (Optional) Seed initial data:

```bash
php artisan db:seed
```

---

## Running the Application

### Development (all services concurrently)

```bash
composer run dev
```

This starts:
- `php artisan serve` — Laravel dev server
- `php artisan queue:listen` — Queue worker
- `php artisan pail` — Log viewer
- `npm run dev` — Vite HMR

### Build for Production

```bash
npm run build
```

### One-command Setup (fresh install)

```bash
composer run setup
```

This runs: `composer install` → `.env` copy → `key:generate` → `migrate` → `npm install` → `npm run build`.

---

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Auth/
│       │   └── StaffLoginController.php   # Staff-specific auth
│       ├── DashboardController.php         # Role-aware dashboard + PDF export
│       ├── PropertiesController.php
│       ├── RenterController.php            # Renters + Owners
│       ├── ViewingsController.php
│       ├── StaffLeasesController.php
│       ├── LeasesController.php            # Client-side leases
│       ├── LeaseApplicationController.php
│       ├── PropertyListingRequestController.php
│       ├── InspectionController.php
│       ├── ReportController.php            # Manager-only PDF reports
│       └── StaffProfileController.php      # Staff CRUD + Branch management
├── Models/
│   ├── Staff.php          # Custom auth model (staffno PK, non-incrementing)
│   ├── User.php           # Client model (linked to renter via renterno)
│   ├── Properties.php
│   └── Inspection.php

database/
└── migrations/
    ├── create_users_table.php
    ├── create_staff_table.php
    ├── add_renterno_to_users_table.php
    ├── create_lease_application_table.php
    └── create_property_listing_request_table.php

resources/
└── views/
    ├── staff/
    │   ├── dashboard.blade.php
    │   ├── dashboard-pdf.blade.php
    │   ├── properties/
    │   ├── renters/
    │   ├── leases/
    │   ├── viewings/
    │   ├── Inspections/
    │   ├── reports/
    │   └── listing-requests/
    ├── welcome.blade.php
    ├── home.blade.php
    ├── leases.blade.php
    ├── viewings.blade.php
    ├── lease-pdf.blade.php
    ├── applications.blade.php
    └── listing-requests.blade.php

routes/
├── web.php      # All application routes (staff + client)
└── auth.php     # Breeze auth routes (client registration/login)
```

---

## Authentication

The system uses **two independent auth guards**:

| Guard   | Login URL       | Model  | Session key      |
|---------|-----------------|--------|------------------|
| `staff` | `/staff/login`  | `Staff`| `auth:staff`     |
| `web`   | `/login`        | `User` | `auth` (default) |

The `Staff` model uses `staffno` as a non-incrementing string primary key. Client (`User`) accounts are linked to the `renter` table via `renterno`.

> **Security note:** A temporary `/fix-my-password` route exists in `web.php` for development. Remove it before deploying to production.

---

## Key Modules

### Dashboard (Role-aware)

- **Regular staff:** own viewings for the last 7 days (bar chart), assigned properties, pending viewings, pending inspections, active leases
- **Manager:** system-wide totals, 7-day revenue trend (line chart), full inventory mix

### Lease Applications

Workflow: `Pending → Approved / Rejected`

Staff approve or reject via `PATCH /staff/applications/{id}/approve|reject`. Approval auto-creates a lease agreement.

### Property Listing Requests

Owners submit property listing requests via the client portal. Staff review and approve/reject via the staff portal.

### Inspections

Staff create inspection records and mark them complete with an evaluation comment. Completed inspections are archived from the dashboard.

### Viewings

Staff schedule viewings and optionally prefill from a listing request. Feedback submission marks the viewing as `Completed`.

---

## PDF Reporting

PDF generation uses **barryvdh/laravel-dompdf**.

| Report                | Route                                 | Access  |
|-----------------------|---------------------------------------|---------|
| Staff Productivity    | `GET /staff/reports/generate?type=productivity`      | Manager |
| Revenue Collection    | `GET /staff/reports/generate?type=revenue-report`    | Manager |
| Property Inventory    | `GET /staff/reports/generate?type=property-inventory`| Manager |
| Inspection Logs       | `GET /staff/reports/generate?type=inspection-logs`   | Manager |
| Payment History       | `GET /staff/reports/generate?type=payment-history`   | Manager |
| Dashboard Report      | `GET /staff/dashboard/report`         | All staff |
| Lease Agreement PDF   | `GET /leases/pdf`                     | Client  |

---

## Known Issues & Notes

- **Migration placeholders:** The `create_staff_table` migration creates only an `id` + `timestamps` stub. The full `staff` schema with all fields (staffno, firstname, etc.) must already exist in PostgreSQL (likely created via pgAdmin or a separate SQL script). Ensure the table schema matches the `Staff` model's `$fillable` array before running dependent migrations.
- **`/fix-my-password` route:** Development utility — remove from `routes/web.php` before any production deployment.
- **Role enforcement:** Position-based access (`Manager` vs `Regular`) is checked inside controllers, not via middleware. Validate this is consistent across all protected controller methods.
- **Property model duplication:** Both `Properties.php` and `Property.php` exist in `app/Models/`. Consolidate to a single canonical model.
