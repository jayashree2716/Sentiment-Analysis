<?php
require 'config.php';

$category     = $_POST['category'];
$title        = $_POST['title'];
$brand        = $_POST['brand'];
$price        = $_POST['price'];
$discount     = $_POST['discount'];
$short_desc   = $_POST['short_desc'];
$long_desc    = $_POST['long_desc'];
$specs        = $_POST['specifications'];

$final_price = $price - (($price * $discount) / 100);

// Handle images
$uploaded_files = [];

foreach($_FILES['images']['name'] as $key => $name){
    $tmp = $_FILES['images']['tmp_name'][$key];

    $newName = time() . "_" . $name;
    move_uploaded_file($tmp, "images/" . $newName);

    $uploaded_files[] = $newName;
}

$image_json = json_encode($uploaded_files);

// Insert into DB
$stmt = $mysqli->prepare(
    "INSERT INTO products (category, title, brand, price, discount, final_price, short_desc, long_desc, specifications, images)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param("sssdiissss", 
    $category, $title, $brand, $price, $discount, $final_price, 
    $short_desc, $long_desc, $specs, $image_json
);

$stmt->execute();

echo "Product added successfully!";
echo "<br><a href='admin_add_product.php'>Add another</a>";
echo "<br><a href='index.php'>Home</a>";
?>
