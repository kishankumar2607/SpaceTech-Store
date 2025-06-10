# SpaceTech Store

**Group Project** by Kishan Kumar Das, Bibin Tom Joseph, and Al Shifan

A simple PHP/MySQL–powered electronics storefront where customers can browse, filter, and add items to their cart. Admins can log in separately to manage products and orders.

## Table of Contents

1. [Features](#features)
2. [Tech Stack](#tech-stack)
3. [Installation](#installation)
4. [Database Setup](#database-setup)
5. [Running the App](#running-the-app)
6. [Folder Structure](#folder-structure)
7. [Authors](#authors)
8. [License](#license)

---

## Features

- Browse all in‑stock products
- Filter by category or search by name
- Add products to cart (session‑based)
- Customer login & registration
- Admin dashboard (separate login)
- Automatic table creation script

---

## Tech Stack

- **Backend:** PHP 8+ with PDO
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3
- **Session Management:** PHP `$_SESSION`
- **Version Control:** Git / GitHub

---

## Installation

1. **Clone the repo**  
   ```bash
   git clone https://github.com/kishankumar2607/SpaceTech-Store.git
   cd SpaceTech-Store
   ```

2. **Install dependencies**  
   (No external dependencies—just PHP and a web server.)

3. **Configure your web server**  
   Point your document root to the project folder.  
   Example with PHP’s built‑in server:  
   ```bash
   php -S localhost:8000
   ```
   Then visit `http://localhost:8000` in your browser.

---

## Database Setup

### Create the database
```sql
CREATE DATABASE IF NOT EXISTS ecommerce_group_project
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

### Automatic table creation script

You can run the provided setup script:
```bash
php setup_db.php
```

Or execute this SQL directly:
```sql
USE ecommerce_group_project;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category VARCHAR(100),
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
```

### Configure `includes/db.php`

Make sure your credentials match:
```php
<?php
$host   = 'localhost';
$dbname = 'ecommerce_group_project';
$user   = 'root';
$pass   = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("DB connection failed: " . $e->getMessage());
}
```

---

## Running the App

1. Make sure your web server is running and PHP is installed.  
2. Navigate to `http://localhost:8000/index.php`.  
3. To access the admin dashboard, go to `http://localhost:8000/admin/login.php`.

---

## Folder Structure

```
/
├── assets/             # CSS, JS, images
├── includes/           # Database connection, helper scripts
├── uploads/            # Product images
├── admin/              # Admin‑only pages
│   └── login.php
├── cart_add.php
├── cart.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── setup_db.php        # Runs the SQL above to create tables
└── README.md
```

---

## Authors

- Kishan Kumar Das  
- Bibin Tom Joseph  
- Al Shifan

