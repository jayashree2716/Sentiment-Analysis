<?php
require 'config.php';   // already contains session_start()
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title> shop— Home</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <!-- Top bar -->
  <header class="topbar">
    <div class="topbar-inner">
      <div class="logo">
        <img src="images/logo.png" alt="Logo" class="logo-img">
        <span class="brand">shop<span>sphere</span></span>
      </div>

      <div class="search-wrap">
        <input id="search" type="search" placeholder="Search for Products, Brands and More" />
        <button id="searchBtn">Search</button>
      </div>

      <div class="actions">
        <div class="nav-right">
    <?php if (isset($_SESSION['user_id'])): ?>
        
        <span class="username">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>   </span>
        <a href="logout.php" class="btn logout-btn">Logout</a>

    <?php else: ?>

        <a href="login.php" class="btn login-btn">Login</a>
        <a href="register.php" class="btn register-btn">Register</a>

    <?php endif; ?>
</div>

    </div>
  </header>

  <!-- Categories row (horizontally scrollable) -->
  <nav class="categories-bar">
    <div class="categories-inner">
      <!-- Repeat category items. Put actual icons in images/categories/ -->
      <a class="cat" href="mobile.php">
        <img src="images/categories/mobiles.png" alt="Mobiles">
        <span>Mobiles</span>
      </a>
      <a class="cat" href="laptop.php">
        <img src="images/categories/laptops.png" alt="Laptops">
        <span>Laptops</span>
      </a>
      <a class="cat" href="">
        <img src="images/categories/fashion.png" alt="Fashion">
        <span>Fashion</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/electronics.png" alt="Electronics">
        <span>Electronics</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/home.png" alt="Home">
        <span>Home</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/tv.png" alt="TVs">
        <span>TVs</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/gaming.png" alt="Gaming">
        <span>Gaming</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/speakers.png" alt="Speakers">
        <span>Speakers</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/camera.png" alt="Cameras">
        <span>Cameras</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/beauty.png" alt="Beauty">
        <span>Beauty</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/grocery.png" alt="Grocery">
        <span>Grocery</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/accessories.png" alt="Accessories">
        <span>Accessories</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/fitness.png" alt="Fitness">
        <span>Fitness</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/others.png" alt="More">
        <span>More</span>
      </a>
    </div>
  </nav>

  <!-- Hero slider -->
  <section class="hero">
    <div class="slider" id="heroSlider">
      <!-- Here I use your uploaded image path as requested -->
      <div class="slide"><img src="images/banners/banner1.jpg" alt="Hero banner 1" style=""></div>
      <div class="slide"><img src="images/banners/banner2.jpg" alt="Hero banner 2"></div>
      <div class="slide"><img src="images/banners/banner3.jpg" alt="Hero banner 3"></div>
    </div>
    <div class="slider-controls">
      <button id="prev">&lt;</button>
      <div id="dots"></div>
      <button id="next">&gt;</button>
    </div>
  </section>

  <!-- Example product sections -->
  <main class="main-content">
    <section class="strip">
      <h2>Top Deals</h2>
      <div class="product-row">
        <!-- sample product card -->
        <article class="card">
          <img src="images/products/p1.jpg" alt="p1">
          <h3>Aurora Pixel Phone</h3>
          <p class="price">₹24,999</p>
        </article>
        <article class="card">
          <img src="images/products/p2.jpg" alt="p2">
          <h3>Skyline Pro X</h3>
          <p class="price">₹32,999</p>
        </article>
        <article class="card">
          <img src="images/products/p3.jpg" alt="p3">
          <h3>Pocket Mini Speaker</h3>
          <p class="price">₹1,999</p>
        </article>
        <article class="card">
          <img src="images/products/p4.jpg" alt="p4">
          <h3>Ultra Laptop 14"</h3>
          <p class="price">₹54,999</p>
        </article>
        <article class="card">
          <img src="images/products/p1.jpg" alt="p1">
          <h3>Aurora Pixel Phone</h3>
          <p class="price">₹24,999</p>
        </article>
      </div>
    </section>

    <section class="strip" style="background:#7aa7e2;">
      <h2>Recommended for You</h2>
      <div class="product-row">
        <article class="card"><img src="images/products/p5.jpg" alt=""><h3>Smart Watch S</h3><p class="price">₹4,999</p></article>
        <article class="card"><img src="images/products/p6.jpg" alt=""><h3>Noise Headphones</h3><p class="price">₹2,999</p></article>
        <article class="card"><img src="images/products/p7.jpg" alt=""><h3>Wireless Charger</h3><p class="price">₹499</p></article>
        <article class="card"><img src="images/products/p8.jpg" alt=""><h3>Gaming Mouse</h3><p class="price">₹1,299</p></article>
      <article class="card"><img src="images/products/p8.jpg" alt=""><h3>Gaming Mouse</h3><p class="price">₹1,299</p></article>
      
      </div>
    </section>
  </main>

  
<footer style="background-color: #212121; color: #f0f0f0; padding: 40px 20px 0; font-family: Arial, sans-serif;margin-top:30px">
    
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; max-width: 1200px; margin: 0 auto 30px; border-bottom: 1px solid #3a3a3a;">
        
        <div style="flex: 1; min-width: 200px; margin-bottom: 20px; padding: 0 15px;">
            <h3 style="color: #ff9800; font-size: 1.6em; font-weight: bold; margin-bottom: 10px;">
                ShopSphere
            </h3>
            <p style="font-size: 0.9em; line-height: 1.5; margin-bottom: 15px; color: #ccc;">
                Your one-stop destination for quality products and unbeatable deals.
            </p>
            <div class="social-links" style="margin-top: 15px;">
                <a href="#" aria-label="Facebook" style="color: #ccc; text-decoration: none; font-size: 1.4em; margin-right: 15px;">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" aria-label="Twitter" style="color: #ccc; text-decoration: none; font-size: 1.4em; margin-right: 15px;">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" aria-label="Instagram" style="color: #ccc; text-decoration: none; font-size: 1.4em; margin-right: 15px;">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>
        </div>

        <div style="flex: 1; min-width: 200px; margin-bottom: 20px; padding: 0 15px;">
            <h4 style="font-size: 1.1em; font-weight: bold; margin-bottom: 15px; color: #ffffff; border-left: 3px solid #ff9800; padding-left: 10px;">
                Quick Links
            </h4>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">Home</a></li>
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">Shop</a></li>
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">New Arrivals</a></li>
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">Sale</a></li>
            </ul>
        </div>

        <div style="flex: 1; min-width: 200px; margin-bottom: 20px; padding: 0 15px;">
            <h4 style="font-size: 1.1em; font-weight: bold; margin-bottom: 15px; color: #ffffff; border-left: 3px solid #ff9800; padding-left: 10px;">
                Customer Service
            </h4>
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">Contact Us</a></li>
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">Track Order</a></li>
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">Returns</a></li>
                <li style="margin-bottom: 8px;"><a href="#" style="color: #ccc; text-decoration: none; font-size: 0.9em;">FAQ</a></li>
            </ul>
        </div>

        <div style="flex: 1; min-width: 200px; margin-bottom: 20px; padding: 0 15px;">
            <h4 style="font-size: 1.1em; font-weight: bold; margin-bottom: 15px; color: #ffffff; border-left: 3px solid #ff9800; padding-left: 10px;">
                Contact Info
            </h4>
            <p style="font-size: 0.9em; margin-bottom: 10px; color: #ccc;"><i class="fas fa-map-marker-alt" style="margin-right: 8px; color: #ff9800;"></i> 123 E-Commerce St, Tech City</p>
            <p style="font-size: 0.9em; margin-bottom: 10px; color: #ccc;"><i class="fas fa-phone" style="margin-right: 8px; color: #ff9800;"></i> (555) 123-4567</p>
            <p style="font-size: 0.9em; margin-bottom: 10px;"><i class="fas fa-envelope" style="margin-right: 8px; color: #ff9800;"></i> <a href="mailto:support@shopsphere.com" style="color: #ff9800; text-decoration: none;">support@shopsphere.com</a></p>
        </div>

    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; max-width: 1200px; margin: 0 auto; padding: 20px 0; font-size: 0.85em; color: #999;">
        <p style="margin: 5px 0;"></p>
        <div style="margin: 5px 0;">
            <i class="fab fa-cc-visa" style="font-size: 2em; margin-left: 15px; color: #ccc;"></i>
            <i class="fab fa-cc-mastercard" style="font-size: 2em; margin-left: 15px; color: #ccc;"></i>
            <i class="fab fa-cc-paypal" style="font-size: 2em; margin-left: 15px; color: #ccc;"></i>
            <i class="fab fa-cc-amex" style="font-size: 2em; margin-left: 15px; color: #ccc;"></i>
        </div>
    </div>
</footer>

  <script src="scripts.js"></script>
</body>
</html>
