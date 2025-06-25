<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


# Laravel Customer Importer API

This Laravel project provides a RESTful API that imports customer data from a third-party provider ([randomuser.me](https://randomuser.me)) and stores it in a local database using the Doctrine ORM. The API supports fetching a list of customers and viewing detailed information for a single customer.

## Features

- Imports Australian customers from the Random User API
- Stores only necessary customer data
- Passwords are hashed using `md5` (as per test requirement)
- API endpoints:
  - `GET /api/customers` – List all customers
  - `GET /api/customers/{id}` – Show a single customer’s details
- Uses a reusable importer service
- Fully tested with mock HTTP responses

---

## 📦 Requirements

- PHP 8.4.6
- Composer
- Laravel 12.19.3
- MySQL
> **Note:** This implementation uses Laravel Eloquent ORM for simplicity. Doctrine ORM can be integrated if later on if really required.
- Git (if cloning from repo)

---

## ⚙️ Installation

```bash
git clone https://github.com/your-username/customer-importer.git
cd customer-importer
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate


## ⚙️ PHPUnit Test

```bash
php artisan test tests/Unit/ tests/Feature/CustomerControllerTest.php
php artisan test tests/Unit/ tests/Feature/CustomerControllerTest.php
php artisan test tests/Unit/ tests/Unit/ImportCustomersJobTest.php
php artisan test tests/Unit/ tests/Unit/RandomUserImporterTest.php


## ⚙️ end-to-end Test
```bash
php artisan import:customers 5