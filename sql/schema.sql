-- Topup Alert Limited - Digital Payments Platform
-- Database Schema
-- Akure, Nigeria | November 2018 – May 2020

CREATE DATABASE IF NOT EXISTS topup_alert CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE topup_alert;

-- Admins / Staff
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('superadmin', 'admin', 'support') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_code VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20) NOT NULL UNIQUE,
    password_hash VARCHAR(255) DEFAULT NULL,
    state VARCHAR(50) DEFAULT 'Ondo',
    city VARCHAR(50) DEFAULT 'Akure',
    address TEXT,
    wallet_balance DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone),
    INDEX idx_status (status)
);

-- Customer Applications (KYC / Account Opening)
CREATE TABLE IF NOT EXISTS customer_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    application_code VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other') DEFAULT 'Male',
    state VARCHAR(50) DEFAULT 'Ondo',
    city VARCHAR(50) DEFAULT 'Akure',
    address TEXT,
    id_type ENUM('NIN', 'Voters Card', 'Drivers License', 'Passport') DEFAULT 'NIN',
    id_number VARCHAR(50),
    status ENUM('pending', 'approved', 'rejected', 'under_review') DEFAULT 'pending',
    rejection_reason TEXT,
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewed_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_phone (phone)
);

-- Service Types
CREATE TABLE IF NOT EXISTS service_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE
);

-- Transactions
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_ref VARCHAR(30) NOT NULL UNIQUE,
    customer_id INT NOT NULL,
    service_type_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    fee DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'NGN',
    status ENUM('pending', 'successful', 'failed', 'reversed') DEFAULT 'pending',
    provider_ref VARCHAR(100),
    beneficiary_phone VARCHAR(20),
    beneficiary_meter VARCHAR(50),
    beneficiary_smartcard VARCHAR(50),
    network ENUM('MTN', 'Airtel', 'Glo', '9mobile', 'Other') NULL,
    data_plan VARCHAR(100) NULL,
    disco ENUM('BEDC', 'EKEDC', 'IKEDC', 'AEDC', 'PHED', 'JED', 'KEDCO', 'Other') NULL,
    cable_provider ENUM('DStv', 'GOtv', 'Startimes', 'Other') NULL,
    payment_method ENUM('wallet', 'card', 'bank_transfer', 'ussd') DEFAULT 'wallet',
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (service_type_id) REFERENCES service_types(id),
    INDEX idx_customer (customer_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_ref (transaction_ref)
);

-- Wallet Transactions (ledger)
CREATE TABLE IF NOT EXISTS wallet_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    transaction_id INT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    balance_before DECIMAL(12,2) NOT NULL,
    balance_after DECIMAL(12,2) NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE SET NULL
);

-- Insert default admin (password: admin123)
INSERT INTO admins (username, email, password_hash, full_name, role) VALUES
('admin', 'admin@topupalert.ng', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Adebayo Okonkwo', 'superadmin'),
('support', 'support@topupalert.ng', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Chioma Adeyemi', 'support');

-- Service Types
INSERT INTO service_types (name, code, description, icon) VALUES
('Airtime Top-up', 'AIRTIME', 'Buy airtime for MTN, Airtel, Glo, 9mobile', 'phone'),
('Data Subscription', 'DATA', 'Internet data bundles for all networks', 'wifi'),
('Electricity Payment', 'ELECTRICITY', 'Pay electricity bills (BEDC, EKEDC, etc.)', 'bolt'),
('Cable TV Subscription', 'CABLE', 'DStv, GOtv, Startimes subscriptions', 'tv');

-- Test Customer Account (password: user123)
-- Login with phone: 08031234567  or email: testuser@topupalert.ng
INSERT INTO customers (customer_code, first_name, last_name, email, phone, password_hash, state, city, address, wallet_balance, status) VALUES
('CUS_TEST01', 'Tunde', 'Adebayo', 'testuser@topupalert.ng', '08031234567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ondo', 'Akure', '15 Adesida Road, Akure', 50000.00, 'active');
