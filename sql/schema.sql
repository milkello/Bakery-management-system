-- Bakery Management System schema (simplified)
CREATE DATABASE IF NOT EXISTS bakery_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bakery_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  email VARCHAR(150) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','manager','staff') DEFAULT 'staff',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS employees (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  dob DATE,
  address TEXT,
  phone VARCHAR(30),
  email VARCHAR(150),
  profile_pic VARCHAR(255),
  salary_type ENUM('monthly','hourly') DEFAULT 'monthly',
  salary_amount DECIMAL(10,2) DEFAULT 0,
  bank_name VARCHAR(150),
  bank_account VARCHAR(100),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS suppliers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  phone VARCHAR(50),
  email VARCHAR(150),
  address TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS raw_materials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  unit VARCHAR(20),
  category VARCHAR(100),
  unit_cost DECIMAL(10,4) DEFAULT 0,
  stock_quantity DECIMAL(12,3) DEFAULT 0,
  low_threshold DECIMAL(12,3) DEFAULT 0,
  supplier_id INT,
  last_purchase_date DATETIME,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS purchases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  supplier_id INT,
  material_id INT,
  quantity DECIMAL(12,3),
  unit_cost DECIMAL(10,4),
  total_cost DECIMAL(12,2),
  invoice_no VARCHAR(100),
  created_by INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  sku VARCHAR(80),
  price DECIMAL(10,2) DEFAULT 0,
  unit VARCHAR(20),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  yield_quantity DECIMAL(10,3) DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS recipe_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  material_id INT NOT NULL,
  qty DECIMAL(12,3) NOT NULL,
  unit VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS production_batches (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  quantity_produced INT NOT NULL,
  produced_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  created_by INT,
  notes TEXT
);
CREATE TABLE IF NOT EXISTS production_materials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  production_id INT NOT NULL,
  material_id INT NOT NULL,
  qty_used DECIMAL(12,3),
  unit VARCHAR(20),
  unit_cost DECIMAL(10,4),
  total_cost DECIMAL(12,2)
);

CREATE TABLE IF NOT EXISTS sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  qty INT NOT NULL,
  unit_price DECIMAL(10,2),
  total_price DECIMAL(12,2),
  customer_type VARCHAR(50),
  payment_method VARCHAR(50),
  created_by INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payrolls (
  id INT AUTO_INCREMENT PRIMARY KEY,
  employee_id INT,
  amount DECIMAL(12,2),
  status ENUM('pending','sent','failed','paid') DEFAULT 'pending',
  transaction_ref VARCHAR(255),
  created_by INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(50),
  message TEXT,
  data JSON,
  is_read TINYINT(1) DEFAULT 0,
  created_by INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(150),
  meta JSON,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
