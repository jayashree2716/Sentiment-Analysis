-- Create DB and tables
CREATE DATABASE IF NOT EXISTS ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ecommerce;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  short_desc VARCHAR(255) NOT NULL,
  long_desc TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  images TEXT NOT NULL, -- JSON array of image filenames
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reviews table
CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  user_id INT NOT NULL,
  rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Products
INSERT INTO products (title, short_desc, long_desc, price, images) VALUES
('Colorful T-Shirt', 'Comfortable cotton t-shirt', 'A vibrant, colorful cotton t-shirt perfect for summer.', 19.99, '["tshirt1.jpg","tshirt2.jpg","tshirt3.jpg"]'),
('Running Sneakers', 'Lightweight running shoes', 'High-performance running shoes with breathable material.', 79.99, '["sneaker1.jpg","sneaker2.jpg","sneaker3.jpg"]'),
('Smartwatch X100', 'Advanced smartwatch', 'Feature-rich smartwatch with fitness tracking and notifications.', 149.99, '["watch1.jpg","watch2.jpg","watch3.jpg"]');
