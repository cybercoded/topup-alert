# Topup Alert Limited — Digital Payments Platform

**Location:** Akure, Nigeria  
**Period:** November 2018 – May 2020 (Demo reconstruction)

A fully functional demo of an automated digital services platform for:

- **Airtime** top-ups (MTN, Airtel, Glo, 9mobile)
- **Data** subscriptions (daily / weekly / monthly plans)
- **Electricity** bill payments (BEDC, EKEDC, IKEDC, AEDC, PHED, JED, KEDCO)
- **Cable TV** subscriptions (DStv, GOtv, Startimes)

---

## Tech Stack

| Layer       | Technology          |
|-------------|---------------------|
| Frontend    | HTML5, CSS3, JavaScript |
| Backend     | PHP 8+ (PDO)        |
| Database    | MySQL 5.7+ / MariaDB |
| Design      | Custom modern UI (Inter + Font Awesome) |

---

## Features

### Customer Portal (`/user/`)
- Login with test account
- Wallet balance (₦50,000 starting balance)
- Buy Airtime (all networks)
- Buy Data bundles (network-specific plans)
- Pay Electricity bills
- Subscribe to DStv / GOtv / Startimes
- Transaction history
- Real-time wallet deduction

### Admin Dashboard
- Authentication
- Customer management (200 seeded)
- KYC / Applications workflow (200 seeded)
- Transaction monitoring (200 seeded)
- Service breakdown & stats
- Search, filter & pagination

---

## Quick Start

### 1. Requirements
- PHP 8.0+
- MySQL / MariaDB
- Apache / Nginx (or PHP built-in server)

### 2. Database Setup

```bash
mysql -u root -p < sql/schema.sql
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
cd topup-alert
php -S localhost:8000
```

Open: **http://localhost:8000**

---

## Login Credentials

| Role     | Login                        | Password  | Notes                    |
|----------|------------------------------|-----------|--------------------------|
| Customer | `08031234567` or `testuser@topupalert.ng` | `user123` | Wallet: ₦50,000         |
| Admin    | `admin`                      | `admin123`| Full dashboard access    |

---

## Project Structure

```
topup-alert/
├── index.php                 # Landing page (Customer / Admin choice)
├── login.php                 # Admin login
├── dashboard.php             # Admin dashboard
├── user/
│   ├── login.php             # Customer login
│   ├── dashboard.php         # Customer home + quick services
│   ├── airtime.php           # Buy airtime
│   ├── data.php              # Buy data bundles
│   ├── electricity.php       # Pay electricity
│   ├── cable.php             # Cable TV subscription
│   ├── history.php           # Transaction history
│   ├── process.php           # Payment processor
│   └── includes/             # Customer header/footer
├── admin/
│   ├── customers.php
│   ├── applications.php
│   └── transactions.php
├── sql/
│   ├── schema.sql            # Tables + test customer + services
│   └── seed.sql              # 200 customers + apps + txns
└── assets/
```

---

## Notes

- This is a **demo system** for portfolio / presentation purposes.
- Payment gateway & third-party API integrations are **simulated**.
- Successful payments deduct from the customer wallet and create ledger entries.
- Password hashing uses bcrypt (demo also accepts plain demo passwords).

---

Built to reflect a real 2018–2020 Nigerian fintech product experience.
