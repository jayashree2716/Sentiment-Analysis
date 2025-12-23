<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products from DB
$sql = "SELECT * FROM products WHERE category='Laptops' ORDER BY id DESC";

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mobiles</title>
    <link rel="stylesheet" href="styles.css">
    
    <style>
        /* General Reset and Base Styles */
        a {
    text-decoration: none;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

/* Gallery Container using CSS Grid for 5 columns */
.mobile-gallery {
    display: grid;
    /* Adjust 'repeat' for the number of columns you need, e.g., 5 */
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
    gap: 20px; /* Space between the cards */
    max-width: 1000px;
    margin: 0 auto;
    margin-top: 40px;
    margin-bottom: 30px;
}

/* Individual Mobile Card Styling */
.mobile-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s;
}

.mobile-card:hover {
    transform: translateY(-5px); /* Subtle hover effect */
}

/* Image Styling */
.image-container {
    height: 180px; /* Fixed height for consistent image size */
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    overflow: hidden;
}

.image-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain; /* Ensures the whole image is visible */
}

/* Text Details */
.mobile-name {
    font-size: 1.1em;
    font-weight: bold;
    color: #333;
    margin: 5px 0;
}

.specs {
    font-size: 0.9em;
    color: #666;
    margin: 0 0 10px 0;
    min-height: 40px; /* Ensure space for specs line */
}

.specs-offer span {
    display: inline-block;
    background-color: #ffe0b2; /* Light orange background for the offer banner */
    color: #e65100; /* Darker orange text */
    font-size: 0.75em;
    padding: 3px 8px;
    border-radius: 4px;
    margin-bottom: 10px;
}

/* Price Information Styling */
.price-info {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.mrp-price {
    font-size: 1em;
    color: #999;
    text-decoration: line-through; /* Cross out the MRP price */
}

.discount-price {
    font-size: 1.2em;
    font-weight: bold;
    color: #e53935; /* Discount price in red or a prominent color */
    background-color: #ffcdd2; /* Light red/pink background for prominence */
    padding: 5px 10px;
    border-radius: 4px;
}
    </style>
</head>
<body>

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

</div>
</div>
</header>

<!-- Categories row (horizontally scrollable) -->
  <nav class="categories-bar">
    <div class="categories-inner">
      <!-- Repeat category items. Put actual icons in images/categories/ -->
      <a class="cat" href="">
        <img src="images/categories/mobiles.png" alt="Mobiles">
        <span>Mobiles</span>
      </a>
      <a class="cat" href="#">
        <img src="images/categories/laptops.png" alt="Laptops">
        <span>Laptops</span>
      </a>
      <a class="cat" href="#">
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

  <div class="mobile-gallery">
    <div>
        <a href="product_details.php">
        <div class="mobile-card">
            <div class="image-container">
                <img src="images\products\lap1.jpg" alt="OnePlus 15">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">Lenovo IdeaPad 1 </h3>
                <p class="specs">AMD Ryzen 5 5500U 15.6" HD Thin and Light Laptop (8GB RAM/512GB SSD/Windows 11 Home/Office Home 2024/1 Year ADP Free/Grey/1.6Kg), 82R4011MIN</p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹41,999</span>
                    <span class="discount-price">₹39,999</span>
                </div>
            </div>
        </div>
        </a>
    </div>
        <div class="mobile-card">
            <div class="image-container">
                <img src="images\products\lap2.jpg" alt="oppo">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">HP 15</h3>
                <p class="specs">13th Gen Intel Core i3-1315U (12GB DDR4, 512GB SSD) FHD, Anti-Glare, Micro-Edge, 15.6''/39.6cm, Win11, M365 Basic(1yr)* Office24, Silver, 1.59kg, fd0573TU, FHD Camera w/Shutter Laptop</p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹51000</span>
                    <span class="discount-price">₹49000</span>
                </div>
            </div>
        </div>

        <div class="mobile-card">
            <div class="image-container">
                <img src=" images\products\lap3.jpg  " alt="OnePlus 15">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">HP Pavilion x360</h3>
                <p class="specs">13th Gen Intel Core i5-1335U (16GB DDR4, 512GB SSD) Touchscreen, 14"/35.6cm, FHD, Win 11, Office 21, Silver, 1.51kg, ek1074tu/1148tu, Iris Xe, FPR, 5MP Camera, Backlit KB Laptop</p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹92000</span>
                    <span class="discount-price">₹89000</span>
                </div>
            </div>
        </div>

        <div class="mobile-card">
            <div class="image-container">
                <img src="images\products\lap4.jpg" alt="OnePlus 15">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">Acer Aspire Lite</h3>
                <p class="specs">ntel Core i5 13th Gen - 1334U, 16GB RAM, 512GB SSD, Full HD 15.6"/39.62 cm, Windows 11 Home, MS Office, Steel Gray, 1.59 KG, AL15-53, Metal Body, Thin and Light Premium Laptop</p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹66,999</span>
                    <span class="discount-price">₹61,999</span>
                </div>
            </div>
        </div>
        

        <div class="mobile-card">
            <div class="image-container">
                <img src="images\products\lap5.jpg" alt="OnePlus 15">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">Lenovo LOQ</h3>
                <p class="specs">AMD Ryzen 5 7235HS, NVIDIA RTX 3050A 4GB, 12GB RAM, 512GB SSD, 15.6"(39.6cm), 144Hz, Windows 11, Office Home 2024, Grey, 2.4Kg, 83JC00HNIN, 3 Mon. Game Pass Gaming Laptop</p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹55,999</span>
                    <span class="discount-price">₹53,999</span>
                </div>
            </div>
        </div>

        <div class="mobile-card">
            <div class="image-container">
                <img src="images\products\lap6.jpg" alt="OnePlus 15">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">Primebook 2 Pro 2025 </h3>
                <p class="specs">8GB RAM, 128GB UFS Storage | 14.1-Inch FHD IPS Display | 14 Hours Battery | MediaTek Helio G99 | Android 15 (PrimeOS 3.0) | Backlit Keyboard | in-Built AI (Gray)</p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹79,999</span>
                    <span class="discount-price">₹75,999</span>
                </div>
            </div>
        </div>

        <div class="mobile-card">
            <div class="image-container">
                <img src="images\products\lap7.jpg" alt="OnePlus 15">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">HP 255 G10 </h3>
                <p class="specs">HP 255 G10 Laptop for Home or Work, 16GB RAM, 512GB SSD, 15.6" Full HD, Ryzen 3 7330U (Beats Intel i5-1135G7), HDMI, USB-C, Windows 11 Pro, Business and Fun Ready
 </p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹79,999</span>
                    <span class="discount-price">₹77,999</span>
                </div>
            </div>
        </div>

        <div class="mobile-card">
            <div class="image-container">
                <img src="images\products\lap8.jpg" alt="OnePlus 15">
            </div>
            
            <div class="details">
                <h3 class="mobile-name">Dell 15</h3>
                <p class="specs">13th Generation Intel Core i5-1334U Processor, 16GB RAM, 1TB SSD, FHD 15.6"/39.62 cm Display, Windows 11, MSO'24, Silver, 1.62kg, Backlit Keyboard, 15 Month McAfee, Thin & Light Laptop </p>
                <p class="specs-offer">
                    <span>*Eligible bank offer</span>
                </p>
                
                <div class="price-info">
                    <span class="mrp-price">₹79,999</span>
                    <span class="discount-price">₹77,999</span>
                </div>
            </div>
        </div>


        </div>

        <div style="margin-left:100px;margin-top: 70px;">
            <a href="http://localhost/ecommerce/product_details.php?id=1">
            <img src="images\products\lap_back2.jpg" alt="gif" style="width: 1100px;">
            </a>
        </div>
        <div style="margin-left:30px;margin-top: 70px;">
            <img src="images\products\lap_back.jpg" alt="back">
        </div>


<div class="mobile-gallery">
<?php while($row = $result->fetch_assoc()): ?>

    <?php 
    // ORIGINAL DB VALUE
    $rawImage = $row['image'];

    // If empty or NULL → use a placeholder
    if (!$rawImage || $rawImage === "" || $rawImage === null) {
        $finalImage = "images/uploads/placeholder.png";
    } else {
        // Converts backslash to forward slash
        $cleanPath = str_replace("\\", "/", $rawImage);

        // If only filename → add full folder path
        if (!str_contains($cleanPath, "/")) {
            $finalImage = "images/products/" . $cleanPath;

        } else {
            // Already full path
            $finalImage = $cleanPath;
        }
    }
?>


    <div class="mobile-card">
        <a href="details_laptop.php?id=<?= $row['id']; ?>">
        <div class="image-container">

            <!-- FIXED IMAGE PATH -->
            <img src="<?= $finalImage ?>" alt="<?= $row['title']; ?>">


        </div>

        <div class="details">
            <h3 class="mobile-name"><?= $row['title']; ?></h3>
            <p class="specs"><?= $row['short_desc']; ?></p>

            <p class="specs-offer">
                <span>*Eligible bank offer</span>
            </p>

            <div class="price-info">
                <span class="mrp-price">₹<?= $row['price'] + 2000; ?></span>
                <span class="discount-price">₹<?= $row['price']; ?></span>
            </div>
        </div>
        </a>
    </div>

<?php endwhile; ?>
</div>


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


</body>
</html>