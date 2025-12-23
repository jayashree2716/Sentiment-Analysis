<?php
// product_details.php
require 'config.php'; // must create $mysqli and session_start()

// PRODUCT ID from query
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    echo "Invalid product id."; exit;
}

// Handle review post
$feedback = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    // login required
    if (!isset($_SESSION['user_id'])) {
        $feedback = 'You must be logged in to submit a review.';
    } else {
        $username = $mysqli->real_escape_string($_SESSION['user_name']);
        $comment = trim($_POST['comment'] ?? '');
        $rating = (int)($_POST['rating'] ?? 5);
        if ($comment === '') {
            $feedback = 'Comment cannot be empty.';
        } else {
            $stmt = $mysqli->prepare("INSERT INTO comments (product_id, user_id, username, comment, rating) VALUES (?, ?, ?, ?, ?)");
            $uid = (int)$_SESSION['user_id'];
            $stmt->bind_param('iissi', $product_id, $uid, $username, $comment, $rating);
            if ($stmt->execute()) {
                // reload to show new comment (prevents repost on refresh)
                header("Location: product_details.php?id={$product_id}#reviews");
                exit;
            } else {
                $feedback = 'Could not save review. Try again later.';
            }
        }
    }
}

// Fetch product
$stmt = $mysqli->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    echo "Product not found."; exit;
}
$product = $res->fetch_assoc();

// Prepare images array: prefer JSON images, else fallback to image column
$images = [];
if (!empty($product['images'])) {
    $decoded = json_decode($product['images'], true);
    if (is_array($decoded) && count($decoded)) {
        $images = $decoded;
    }
}
if (empty($images) && !empty($product['image'])) {
    $images[] = $product['image'];
}
// If still empty add placeholder
if (empty($images)) {
    $images[] = 'placeholder.png';
}

// Fetch comments
$comm_stmt = $mysqli->prepare("SELECT * FROM comments WHERE product_id = ? ORDER BY created_at DESC");
$comm_stmt->bind_param('i', $product_id);
$comm_stmt->execute();
$comments = $comm_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// helper for price after discount
$price = (float)$product['price'];

// FIX: prevent "Undefined array key discount_percent"
$discount_percent = isset($product['discount_percent']) ? (int)$product['discount_percent'] : 0;

$discount_amount = round($price * ($discount_percent / 100), 2);
$price_after = round($price - $discount_amount, 2);

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($product['title']); ?> — Product Details</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
/* --- Layout --- */
a {
    text-decoration: none;
}

:root{
  --accent: #ff5c8a;
  --muted: #6b7280;
  --card-bg: #fff;
}
*{box-sizing:border-box}
body{font-family:Inter, Arial, sans-serif;background:#f4f6fb;margin:0;color:#111}
.container{max-width:1200px;margin:18px auto;padding:0 16px}

/* Topbar (brief) */
.topbar{display:flex;align-items:center;justify-content:space-between;background:#fff;padding:12px 18px;border-radius:8px;box-shadow:0 6px 18px rgba(13,38,76,0.05);margin-bottom:16px}
.brand{font-weight:700;font-size:20px;color:var(--accent)}
.top-actions a{margin-left:12px;text-decoration:none;color:#111;padding:8px 12px;border-radius:8px;border:1px solid #eef2ff}

/* Main two-column layout */
.product-wrap{display:grid;grid-template-columns: 320px 1fr;gap:20px;align-items:start}
@media(max-width:980px){ .product-wrap{grid-template-columns:1fr; padding-bottom:20px} }

/* Left: vertical thumbnails + main image */
.left-col{display:flex;gap:12px}
.thumbnail-col{display:flex;flex-direction:column;gap:10px;width:80px}
.thumb{width:80px;height:80px;object-fit:cover;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.08);cursor:pointer;border:2px solid transparent}
.thumb.active{border-color:var(--accent)}
.main-image{flex:1;display:flex;align-items:center;justify-content:center;background:var(--card-bg);border-radius:10px;padding:20px;box-shadow:0 6px 18px rgba(13,38,76,0.06)}
.main-image img{max-width:100%;max-height:520px;object-fit:contain;border-radius:8px}

/* Right: product details */
.right-col{background:var(--card-bg);padding:20px;border-radius:10px;box-shadow:0 6px 18px rgba(13,38,76,0.04)}
.title{font-size:22px;font-weight:700;margin-bottom:6px}
.brand-name{color:var(--muted);margin-bottom:12px}
.price-row{display:flex;gap:12px;align-items:center;margin-bottom:12px}
.price{font-size:22px;font-weight:800;color:var(--accent)}
.orig-price{text-decoration:line-through;color:var(--muted)}
.discount-badge{background:#ffeddc;color:#a64c00;padding:6px 8px;border-radius:6px;font-weight:700}

/* Properties table */
.properties{display:flex;gap:12px;flex-wrap:wrap;margin:14px 0}
.prop{background:#f8fbff;padding:10px;border-radius:8px;min-width:140px;flex:1}
.prop h4{margin:0;font-size:13px;color:var(--muted)}
.prop p{margin:6px 0 0;font-weight:700}

/* About section */
.section{background:var(--card-bg);padding:18px;border-radius:10px;margin-top:18px;box-shadow:0 6px 18px rgba(13,38,76,0.03)}
.section h3{margin-top:0}
.section p{line-height:1.6;color:#333}

/* Feature images full width */
.features{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-top:12px}
.features img{width:100%;height:160px;object-fit:cover;border-radius:8px}
@media(max-width:900px){ .features{grid-template-columns:repeat(2,1fr)} }

/* Reviews */
#reviews {margin-top:18px}
.review-box{background:#fff;padding:12px;border-radius:8px;margin-bottom:10px;box-shadow:0 2px 6px rgba(0,0,0,0.04)}
.review-box .meta{font-size:13px;color:var(--muted);margin-bottom:6px}
.write-review{margin-top:12px}
textarea{width:100%;min-height:90px;padding:10px;border-radius:8px;border:1px solid #e6eefc}
button.cta{background:var(--accent);color:#fff;border:none;padding:10px 16px;border-radius:8px;cursor:pointer}

/* small helpers */
.muted{color:var(--muted)}
.center{display:flex;justify-content:center;align-items:center}
.feedback{margin-top:8px;color:#b31b1b}

/* Responsive small screens */
@media (max-width:700px){
  .thumbnail-col{flex-direction:row;width:auto;overflow:auto}
  .thumb{width:64px;height:64px}
  .left-col{flex-direction:column}
}
</style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <div class="brand">shop<span style="color:#0057ff">Sphere</span></div>
      <div class="top-actions">
        <?php if (isset($_SESSION['user_id'])): ?>
          <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Sign up</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="product-wrap">
      <!-- LEFT: thumbnails & main image -->
      <div class="left-col">
        <div class="thumbnail-col" id="thumbs">
          <?php foreach ($images as $i => $img): ?>
            <img src="images/products/<?php echo htmlspecialchars($img); ?>" class="thumb <?php echo $i===0?'active':''; ?>" data-index="<?php echo $i; ?>">
          <?php endforeach; ?>
        </div>

        <div class="main-image">
         <img id="mainImg" src="images/products/<?php echo htmlspecialchars($images[0]); ?>" alt="Product image">
        </div>
      </div>

      <!-- RIGHT: details -->
      <div class="right-col">
        <div class="title"><?php echo htmlspecialchars($product['title']); ?></div>
        <div class="brand-name">Brand: <?php echo htmlspecialchars($product['brand'] ?? '—'); ?></div>

        <div class="price-row">
          <div class="price">₹<?php echo number_format($price_after,2); ?></div>
          <?php if ($discount_percent > 0): ?>
            <div class="orig-price">₹<?php echo number_format($price,2); ?></div>
            <div class="discount-badge"><?php echo $discount_percent; ?>% OFF</div>
          <?php endif; ?>
        </div>

        
        <div class="properties">
          <div class="prop">
            <h4>Battery</h4>
            <p><?php echo htmlspecialchars($product['battery'] ?? '7,300 mAh'); ?></p>
          </div>
          <div class="prop">
            <h4>Display</h4>
            <p><?php echo htmlspecialchars($product['display'] ?? '6.78-inch 1.5K AMOLED display with a 165Hz refresh rate'); ?></p>
          </div>
          <div class="prop">
            <h4>Processor</h4>
            <p><?php echo htmlspecialchars($product['cpu'] ?? 'Snapdragon 8 Elite Gen 5 processor'); ?></p>
          </div>
          <div class="prop">
            <h4>RAM / Storage</h4>
            <p><?php echo htmlspecialchars(($product['ram'] ?? '4GB or 8GB') . ' / ' . ($product['storage'] ?? '256GB or 512GB UFS 4.1')); ?></p>
          </div>
          <div class="prop">
            <h4>Camera</h4>
            <p><?php echo htmlspecialchars($product['camera'] ?? '50MP rear camera'); ?></p>
          </div>
        </div>

        <div class="section">
          <h3>About this phone</h3>
          <?php
            // If product has 'about' content in DB, display it; else show placeholder ~200+ words
            if (!empty($product['about'])) {
                echo '<p>' . nl2br(htmlspecialchars($product['about'])) . '</p>';
            } else {
                // placeholder content ~200 words
                echo '<p>The ' . htmlspecialchars($product['title']) . ' is engineered for users who want the best combination of power, battery life and display quality. It combines a cutting-edge processor with efficient memory handling to provide smooth multi-tasking performance. Gamers will appreciate the thermal management and frame stability, while photographers will find the camera array capable across wide, ultra-wide and low-light scenarios. The display uses high-contrast panels to reproduce rich colours and deep blacks, while the high refresh rate keeps animations fluid and responsive. The battery management enhances standby times and supports fast charging to minimise downtime.</p>';

                echo '<p>The build of the phone balances aesthetics with durability. The device uses a premium back finish and a reinforced frame to withstand daily use. Connectivity is modern and includes fast Wi-Fi, 5G support, and Bluetooth Low Energy. The software experience is optimised to help users get more done with dedicated productivity features and customizable gestures. Security features such as fingerprint unlock and face unlocking ensure convenience and safety. With an immersive audio system and smart power saving modes, the device fits both entertainment and productivity needs.</p>';

                echo '<p>For photographers, the camera stack delivers flexible focal length options, excellent dynamic range handling and multiple AI-driven modes for scene recognition and night shooting. With optical image stabilization, multi-frame processing and advanced noise reduction, images remain crisp in challenging conditions. Video capture benefits from stabilization and high-resolution recording. The device also supports various accessories such as wireless chargers, cases and additional lenses.</p>';

                echo '<p>Overall, the ' . htmlspecialchars($product['title']) . ' is designed to be a dependable, stylish and performance-oriented smartphone that addresses the needs of everyday users, power users and content creators alike.</p>';
            }
          ?>
        </div>

        <div class="section">
          <h3>About the Company</h3>
          <?php
            if (!empty($product['company_about'])) {
                echo '<p>' . nl2br(htmlspecialchars($product['company_about'])) . '</p>';
            } else {
                echo '<p>Founded with a focus on innovation, the company behind this phone has built its reputation on combining thoughtful engineering with user-friendly design. From research and development to quality assurance and after-sales support, the firm invests heavily to ensure each product meets high standards. Their ecosystem includes accessories and software updates, and they collaborate with partners to enhance user experience continuously. Customer service focuses on transparent warranty policies and effective technical support.</p>';
            }
          ?>
        </div>

        <!-- Feature photos -->
        <div class="section">
          <h3>Product Features</h3>
          <div class="features">
            <?php
              // show first four images or feature placeholders
              $feature_imgs = array_slice($images, 0, 4);
              while (count($feature_imgs) < 4) $feature_imgs[] = 'placeholder.png';
              foreach ($feature_imgs as $fi) {
    echo '<img src="images/products/' . htmlspecialchars($fi) . '" alt="feature">';
}

            ?>
          </div>
        </div>

          <div >
            <button style="width: 100px;height:40px;background-color:orange;font-size:20px;margin-left:540px;margin-top:40px;border-radius:10px">buy</button>
            <button style="width: 100px;height:40px;background-color:orange;font-size:20px;margin-left:640px;position:relative;bottom:40px;left:30px;border-radius:10px">Add-Cart</button>
          </div>

        <!-- Reviews -->
        <div id="reviews" class="section" style="position:absolute;left:40px;bottom:-900px;width:380px;height: 600px;      overflow-y: auto;       overflow-x: hidden;">
          <h3>Reviews</h3>
          <?php if ($feedback): ?>
            <div class="feedback"><?php echo htmlspecialchars($feedback); ?></div>
          <?php endif; ?>

          <?php if (count($comments) === 0): ?>
            <p class="muted">No reviews yet — be the first!</p>
          <?php else: ?>
            <?php foreach ($comments as $c): ?>
              <div class="review-box">
                <div class="meta"><strong><?php echo htmlspecialchars($c['username']); ?></strong> • <span class="muted"><?php echo htmlspecialchars($c['created_at']); ?></span> • Rating: <?php echo (int)$c['rating']; ?>/5</div>
                <div class="text"><?php echo nl2br(htmlspecialchars($c['comment'])); ?></div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <div class="write-review" style="position:relative;left:20px">
            <?php if (!isset($_SESSION['user_id'])): ?>
              <p class="muted">
                <a href="login.php" style="text-decoration:none;width:30px;height: 50px;padding:10px;border:solid">login</a> 
                to  review.</p>
            <?php else: ?>
              <form method="post" action="product_details.php?id=<?php echo $product_id; ?>#reviews">
                <input type="hidden" name="action" value="add_review">
                <label>Rating:
                  <select name="rating">
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Very good</option>
                    <option value="3">3 - Good</option>
                    <option value="2">2 - Ok</option>
                    <option value="1">1 - Poor</option>
                  </select>
                </label>
                <textarea name="comment" placeholder="Write your review here..." required></textarea>
                <div style="margin-top:8px;">
                  <button type="submit" class="cta">Submit review</button>
                </div>
              </form>
            <?php endif; ?>
          </div>

        </div>

      </div> <!-- end right-col -->
    </div> <!-- end product-wrap -->

  </div> <!-- end container -->
<a href="sentiment_graph.php?product_id=<?php echo $product_id; ?>">
    <button style="margin-top:10px;background:green;color:white;padding:8px 12px;border:none;border-radius:5px;cursor:pointer;position:relative;left:70px;bottom:40px">
        View Sentiment Analysis
    </button>
</a>

<script>
// Thumbnail click -> change main image & active class
document.addEventListener('DOMContentLoaded', function(){
  const thumbs = document.querySelectorAll('.thumb');
  const mainImg = document.getElementById('mainImg');
  thumbs.forEach(t => {
    t.addEventListener('click', () => {
      // remove active
      thumbs.forEach(x => x.classList.remove('active'));
      t.classList.add('active');
      const src = t.getAttribute('src');
      mainImg.setAttribute('src', src);
    });
  });

  // small keyboard nav: left/right to cycle
  let current = 0;
  function setActive(index){
    if (!thumbs.length) return;
    thumbs.forEach(x=>x.classList.remove('active'));
    const t = thumbs[index % thumbs.length];
    if (t) {
      t.classList.add('active');
      mainImg.src = t.src;
    }
  }
  // attach arrow keys
  document.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight') { current = (current + 1) % thumbs.length; setActive(current); }
    if (e.key === 'ArrowLeft')  { current = (current - 1 + thumbs.length) % thumbs.length; setActive(current); }
  });
});
</script>
</body>
</html>
