<?php
require 'config.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (!$name || !$email || !$password) $errors[] = 'All fields required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (strlen($password) < 6) $errors[] = 'Password at least 6 chars.';

    if (empty($errors)){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()){
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            header('Location: index.php'); exit;
        } else {
            if ($mysqli->errno === 1062) $errors[] = 'Email already registered.';
            else $errors[] = 'DB error.';
        }
    }
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h2>Sign Up</h2>
    <?php if(!empty($errors)): ?>
      <div class="errors"><?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?></div>
    <?php endif; ?>
    <form method="post" action="register.php" novalidate>
      <input type="text" name="name" placeholder="Full Name" value="<?php echo isset($_POST['name'])?htmlspecialchars($_POST['name']):''; ?>" required>
      <input type="email" name="email" placeholder="Email address" value="<?php echo isset($_POST['email'])?htmlspecialchars($_POST['email']):''; ?>" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm" placeholder="Confirm Password" required>
      <button type="submit" class="btn">Sign Up</button>
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
  </div>
</body>
</html>
