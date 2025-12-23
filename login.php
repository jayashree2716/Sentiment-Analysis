<?php
require 'config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if (!$email || !$password) $errors[] = 'Enter email & password.';
    if (empty($errors)){
        $stmt = $mysqli->prepare('SELECT id, name, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()){
            if (password_verify($password, $row['password_hash'])){
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                header('Location: index.php'); exit;
            } else $errors[] = 'Wrong credentials.';
        } else $errors[] = 'No user with that email.';
    }
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h2>Login</h2>
    <?php if(!empty($errors)): ?>
      <div class="errors"><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
    <?php endif; ?>
    <form method="post" action="login.php" novalidate>
      <input type="email" name="email" placeholder="Email address" value="<?php echo isset($_POST['email'])?htmlspecialchars($_POST['email']):''; ?>" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Sign up here</a>.</p>
  </div>
</body>
</html>
