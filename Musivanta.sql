-- Musivanta: Web-Based Musician Reservation and Payment Management System
-- MySQL 8+ / MariaDB 10.5+ | InnoDB
-- Import this file first using phpMyAdmin or the MySQL command line.

CREATE DATABASE IF NOT EXISTS musician_booking_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE musician_booking_db;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS availability;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS musicians;
DROP TABLE IF EXISTS genres;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    user_id VARCHAR(20) PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','musician','customer') NOT NULL DEFAULT 'customer',
    account_status ENUM('active','inactive','suspended') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_role_status (role, account_status)
) ENGINE=InnoDB;

CREATE TABLE genres (
    genre_id VARCHAR(20) PRIMARY KEY,
    genre_name VARCHAR(80) NOT NULL UNIQUE,
    description VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE musicians (
    musician_id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL UNIQUE,
    stage_name VARCHAR(120) NOT NULL,
    biography TEXT NULL,
    location VARCHAR(150) NULL,
    experience_years TINYINT UNSIGNED NOT NULL DEFAULT 0,
    profile_image VARCHAR(255) NULL,
    verification_status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_musicians_user FOREIGN KEY (user_id) REFERENCES users(user_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_musicians_stage_name (stage_name),
    INDEX idx_musicians_location (location),
    INDEX idx_musicians_verification (verification_status)
) ENGINE=InnoDB;

CREATE TABLE services (
    service_id VARCHAR(20) PRIMARY KEY,
    musician_id VARCHAR(20) NOT NULL,
    service_name VARCHAR(120) NOT NULL,
    description TEXT NULL,
    rate DECIMAL(10,2) NOT NULL,
    rate_unit ENUM('per_hour','per_event','per_day') NOT NULL DEFAULT 'per_event',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_services_musician FOREIGN KEY (musician_id) REFERENCES musicians(musician_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_services_musician_active (musician_id, is_active)
) ENGINE=InnoDB;

CREATE TABLE availability (
    availability_id VARCHAR(20) PRIMARY KEY,
    musician_id VARCHAR(20) NOT NULL,
    available_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    availability_status ENUM('available','unavailable','booked') NOT NULL DEFAULT 'available',
    notes VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_availability_musician FOREIGN KEY (musician_id) REFERENCES musicians(musician_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_availability_time CHECK (end_time > start_time),
    INDEX idx_availability_lookup (musician_id, available_date, availability_status)
) ENGINE=InnoDB;

CREATE TABLE bookings (
    booking_id VARCHAR(20) PRIMARY KEY,
    customer_id VARCHAR(20) NOT NULL,
    musician_id VARCHAR(20) NOT NULL,
    service_id VARCHAR(20) NOT NULL,
    event_title VARCHAR(150) NOT NULL,
    event_type VARCHAR(80) NULL,
    event_location VARCHAR(180) NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    guest_count INT UNSIGNED NULL,
    agreed_amount DECIMAL(10,2) NOT NULL,
    booking_status ENUM('pending','confirmed','rejected','cancelled','completed') NOT NULL DEFAULT 'pending',
    customer_notes TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_customer FOREIGN KEY (customer_id) REFERENCES users(user_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_musician FOREIGN KEY (musician_id) REFERENCES musicians(musician_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_service FOREIGN KEY (service_id) REFERENCES services(service_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_booking_time CHECK (end_time > start_time),
    CONSTRAINT chk_booking_amount CHECK (agreed_amount >= 0),
    INDEX idx_bookings_customer (customer_id, created_at),
    INDEX idx_bookings_musician_date (musician_id, event_date, booking_status),
    INDEX idx_bookings_status (booking_status)
) ENGINE=InnoDB;

CREATE TABLE payments (
    payment_id VARCHAR(20) PRIMARY KEY,
    booking_id VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('gcash','maya','bank_transfer','cash','other') NOT NULL DEFAULT 'other',
    reference_number VARCHAR(100) NULL,
    proof_image VARCHAR(255) NULL,
    payment_status ENUM('pending','submitted','verified','rejected','refunded') NOT NULL DEFAULT 'pending',
    payment_date DATETIME NULL,
    verified_by VARCHAR(20) NULL,
    verified_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_payments_verifier FOREIGN KEY (verified_by) REFERENCES users(user_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_payment_amount CHECK (amount >= 0),
    INDEX idx_payments_booking (booking_id),
    INDEX idx_payments_status (payment_status)
) ENGINE=InnoDB;

CREATE TABLE notifications (
    notification_id VARCHAR(20) PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    notification_type ENUM('booking','payment','account','system') NOT NULL DEFAULT 'system',
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(user_id)
      ON UPDATE CASCADE ON DELETE RESTRICT,
    INDEX idx_notifications_user_read (user_id, is_read, created_at)
) ENGINE=InnoDB;
