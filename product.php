<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// fetch product
$stmt = $mysqli->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if (!$product = $res->fetch_assoc()) {
    echo 'Product not found';
    exit;
}
$product['images'] = json_decode($product['images']);

// handle review post
$review_err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating']) && isset($_SESSION['user_id'])) {
    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);
    $stmt = $mysqli->prepare('INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('iiis', $id, $_SESSION['user_id'], $rating, $comment);
    if (!$stmt->execute()) $review_err = 'Could not save review.';
    else header("Location: product.php?id=$id"); // redirect to avoid resubmission
}

// fetch reviews
$stmt = $mysqli->prepare('SELECT r.*, u.name FROM reviews r JOIN users u ON u.id=r.user_id WHERE product_id=? ORDER BY r.created_at DESC');
$stmt->bind_param('i', $id);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($product['title']); ?></title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="site-header">
    <div class="brand">Colorful<span>Shop</span></div>
    <nav>
      <?php if(isset($_SESSION['user_id'])): ?>
        <span class="welcome">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
        <a href="logout.php" class="btn">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn">Login</a>
        <a href="register.php" class="btn alt">Sign up</a>
      <?php endif; ?>
      <a href="index.php" class="btn">Home</a>
    </nav>
  </header>

  <main class="container product-detail">
    <h1><?php echo htmlspecialchars($product['title']); ?></h1>
    <div class="product-gallery">
      <?php foreach($product['images'] as $img): ?>
        <img src="images/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
      <?php endforeach; ?>
    </div>
    <div class="product-info">
      <p class="price">$<?php echo number_format($product['price'], 2); ?></p>
      <p class="long-desc"><?php echo nl2br(htmlspecialchars($product['long_desc'])); ?></p>
    </div>

    <section class="reviews">
      <h2>Reviews</h2>
      <?php if (count($reviews) === 0): ?>
        <p>No reviews yet.</p>
      <?php else: ?>
        <?php foreach($reviews as $r): ?>
          <div class="review">
            <strong><?php echo htmlspecialchars($r['name']); ?></strong>
            <span class="rating"><?php echo str_repeat('★', $r['rating']); ?><?php echo str_repeat('☆', 5 - $r['rating']); ?></span>
            <p><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if(isset($_SESSION['user_id'])): ?>
        <form method="post" action="product.php?id=<?php echo $id; ?>" class="review-form" novalidate>
          <?php if($review_err): ?><p class="errors"><?php echo htmlspecialchars($review_err); ?></p><?php endif; ?>
          <label>Rating:
            <select name="rating" required>
              <option value="">Select rating</option>
              <?php for ($i=1; $i<=5; $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?> star<?php echo $i>1?'s':''; ?></option>
              <?php endfor; ?>
            </select>
          </label>
          <label>Comment:
            <textarea name="comment" rows="4"></textarea>
          </label>
          <button type="submit" class="btn">Submit Review</button>
        </form>
      <?php else: ?>
        <p><a href="login.php">Login</a> to leave a review.</p>
      <?php endif; ?>
    </section>
  </main>

  <footer class="site-footer">
    <p>© 2024 Colorful Shop. All rights reserved.</p>
  </footer>
</body>
</html>
<?php
require 'config.php';

$product_id = intval($_GET['id']);  // product id from URL

// Fetch reviews
$stmt = $mysqli->prepare("SELECT c.comment, c.created_at, u.name AS username
                          FROM comments c
                          JOIN users u ON u.id = c.user_id
                          WHERE c.product_id = ?
                          ORDER BY c.created_at DESC");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<div class="review-section">
    <h2>Customer Reviews</h2>

    <?php while ($r = $reviews->fetch_assoc()): ?>
        <div class="review-box">
            <strong><?php echo htmlspecialchars($r['username']); ?></strong>
            <span class="date"><?php echo $r['created_at']; ?></span>
            <p><?php echo htmlspecialchars($r['comment']); ?></p>
        </div>
    <?php endwhile; ?>

    <hr>

    <h3>Write a Review</h3>

    <?php if (!isset($_SESSION['user_id'])): ?>
        <p class="login-msg">You must <a href="login.php">Login</a> to write a review.</p>

    <?php else: ?>

        <form method="POST" action="submit_review.php">
            <textarea name="comment" required placeholder="Write your experience..." rows="4"></textarea>
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <button type="submit" class="btn">Submit Review</button>
        </form>

    <?php endif; ?>
</div>
