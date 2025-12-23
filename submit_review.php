<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$comment = trim($_POST['comment']);

if ($comment == "") {
    die("Comment cannot be empty.");
}

$stmt = $mysqli->prepare("INSERT INTO comments (product_id, user_id, username, comment) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $product_id, $user_id, $_SESSION['user_name'], $comment);
$stmt->execute();

header("Location: product.php?id=" . $product_id);
exit;
?>
