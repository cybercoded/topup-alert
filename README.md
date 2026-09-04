# Topup Alert Limited — Digital Payments Platform

**Location:** Akure, Nigeria  
**Period:** November 2018 – May 2020 (Demo reconstruction)

A fully functional demo of an automated digital services platform for:

- Airtime top-ups (MTN, Airtel, Glo, 9mobile)
- Internet data subscriptions
- Electricity bill payments (BEDC, EKEDC, IKEDC, etc.)
- Cable TV subscriptions (DStv, GOtv, Startimes)

## Tech Stack

| Layer       | Technology          |
|-------------|---------------------|
| Frontend    | HTML5, CSS3, JavaScript |
| Backend     | PHP 8+ (PDO)        |
| Database    | MySQL 5.7+ / MariaDB |
| Design      | Custom modern UI (Inter + Font Awesome) |

## Features

- Admin authentication
- Customer management (200 seeded records)
- KYC / Customer Applications workflow (200 seeded)
- Transaction history & filtering (200 seeded)
- Wallet balances in Nigerian Naira (₦)
- Service breakdown dashboard
- Search, filter & pagination
- Responsive design

## Quick Start

### 1. Requirements
- PHP 8.0+
- MySQL / MariaDB
- Apache / Nginx (or PHP built-in server)

### 2. Database Setup

```bash
# Create database and tables
mysql -u root -p < sql/schema.sql

# Load 200 seed records each for customers, applications & transactions
mysql -u root -p < sql/seed.sql
```

### 3. Configuration

Edit `config/database.php` if needed:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'topup_alert');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Run

```bash
# Using PHP built-in server
cd topup-alert
php -S localhost:8000
```

Open: **http://localhost:8000**

### Demo Login

| Field    | Value     |
|----------|-----------|
| Username | `admin`   |
| Password | `admin123`|

## Project Structure

```
topup-alert/
├── config/database.php      # DB connection & helpers
├── includes/                # Header / Footer
├── assets/css/style.css     # Full design system
├── assets/js/main.js
├── admin/
│   ├── customers.php
│   ├── applications.php
│   └── transactions.php
├── sql/
│   ├── schema.sql           # Tables + default admin + services
│   └── seed.sql             # 200 customers + 200 apps + 200 txns
├── login.php
├── logout.php
├── dashboard.php
└── index.php
```

## Seed Data Highlights

- **Realistic Nigerian names** (Yoruba, Igbo, Hausa)
- **Valid phone prefixes** (MTN, Airtel, Glo, 9mobile)
- **States & cities** across Nigeria (focus on South-West + others)
- **Naira amounts** for airtime, data plans, electricity, cable packages
- **Transaction statuses**: successful / pending / failed / reversed
- **Application statuses**: pending / under_review / approved / rejected

## Notes

- This is a **demo system** for portfolio / presentation purposes.
- Payment gateway & third-party API integrations are simulated.
- Password hashing uses bcrypt (demo accepts `admin123`).

---

Built with care to reflect a real 2018–2020 Nigerian fintech product experience.
