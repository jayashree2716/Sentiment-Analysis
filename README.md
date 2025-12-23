# Colorful E-commerce PHP + MySQL Project

## Overview
This is a colorful, attractive e-commerce website built with PHP, MySQL, HTML, CSS, and JavaScript. It supports user registration/login, product listing with multiple images, detailed product pages, user reviews, and a responsive design.

## Features
- User signup/login/logout with password hashing
- Product listing with search capability
- Product detail pages with multiple images and reviews
- Review submission for logged-in users
- Responsive and colorful UI design

## Requirements
- PHP 7.4+ with mysqli extension enabled
- MySQL server (e.g., MariaDB)
- Web server like Apache or built-in PHP server

## Setup Instructions

1. **Create and Seed Database**
   - Import the database schema and sample data from `db_init.sql`
   ```
   mysql -u root -p < db_init.sql
   ```
   Replace `root` with your MySQL username.

2. **Configure Database Credentials**
   - Open `config.php`
   - Set the `$DB_USER`, `$DB_PASS`, and `$DB_HOST` according to your MySQL setup

3. **Product Images**
   - Place product images in the `images/` directory in the project root
   - Ensure image filenames match those used in the database (e.g., `tshirt1.jpg`)

4. **Run the Website**
   - Use built-in PHP server:
   ```
   php -S localhost:8000
   ```
   - Open `http://localhost:8000/index.php` in a browser

## Additional Notes
- Passwords are securely hashed using PHP's `password_hash` function.
- Review submissions are tied to logged in users only.
- The project currently uses procedural mysqli functions for database queries.
- Frontend uses simple JavaScript and CSS for improved user experience.

## Next Steps (Optional)
- Add cart & checkout flow
- Add admin panel for product and image management
- Improve security with PDO and prepared statements everywhere
- Implement MVC architecture
- Make a polished mobile-first design

## License
MIT License
