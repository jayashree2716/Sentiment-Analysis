<?php
$product_id = $_GET['product_id'];

// Run Python script
$command = escapeshellcmd("python sentiment_analysis.py $product_id");
$output = shell_exec($command);

$base_url = "http://localhost/ecommerce/images/uploads/sentiment";

// DISPLAY FEATURE SENTIMENT
echo "<img src='{$base_url}/feature_sentiment_product_{$product_id}.png?" . time() . "' width='600'><br><br>";

// DISPLAY OVERALL SENTIMENT
echo "<img src='{$base_url}/overall_sentiment_product_{$product_id}.png?" . time() . "' width='600'>";
?>
