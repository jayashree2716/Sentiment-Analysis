<?php
// ************************************
// CONFIG
// ************************************
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 1;

$folder = "images/uploads/sentiment/";

// Image filenames created by Python LLM script
$overall_img = $folder . "overall_sentiment_product_" . $product_id . ".png";
$feature_img = $folder . "feature_sentiment_product_" . $product_id . ".png";
$json_file   = $folder . "summary_product_" . $product_id . ".json";

// Load JSON summary
$summary = [];
if (file_exists($json_file)) {
    $summary = json_decode(file_get_contents($json_file), true);
} else {
    die("<h2>No sentiment JSON found for Product ID $product_id</h2>");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sentiment Analysis Results - Product <?php echo $product_id; ?></title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .card { padding: 20px; border: 1px solid #ccc; border-radius: 10px; margin-bottom: 20px; }
        img { max-width: 100%; height: auto; display: block; margin: auto; }
        table { border-collapse: collapse; width: 70%; margin-top: 10px; }
        table, tr, th, td { border: 1px solid #555; padding: 10px; text-align: center; }
        h2 { margin-bottom: 10px; }
    </style>
</head>
<body>

<h1>Sentiment Analysis Results (LLM) – Product <?php echo $product_id; ?></h1>

<!-- ===================== -->
<!--   OVERALL SENTIMENT   -->
<!-- ===================== -->
<div class="card">
    <h2>Overall Sentiment Summary</h2>

    <p><b>Positive:</b> <?php echo $summary["overall"]["positive"]; ?></p>
    <p><b>Neutral:</b> <?php echo $summary["overall"]["neutral"]; ?></p>
    <p><b>Negative:</b> <?php echo $summary["overall"]["negative"]; ?></p>

    <h3>Overall Sentiment Graph</h3>
    <?php if (file_exists($overall_img)): ?>
        <img src="<?php echo $overall_img; ?>" alt="Overall Sentiment Graph">
    <?php else: ?>
        <p style="color:red;">Overall graph image not found.</p>
    <?php endif; ?>
</div>

<!-- ===================== -->
<!--   FEATURE SENTIMENT   -->
<!-- ===================== -->
<div class="card">
    <h2>Feature-wise Sentiment Summary</h2>

    <table>
        <tr>
            <th>Feature</th>
            <th>Total Mentions</th>
            <th>Positive</th>
            <th>Neutral</th>
            <th>Negative</th>
        </tr>
        <?php foreach ($summary["features"] as $feature => $data): ?>
        <tr>
            <td><?php echo ucfirst($feature); ?></td>
            <td><?php echo $data["count"]; ?></td>
            <td><?php echo $data["positive"]; ?></td>
            <td><?php echo $data["neutral"]; ?></td>
            <td><?php echo $data["negative"]; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3>Feature-wise Sentiment Graph</h3>
    <?php if (file_exists($feature_img)): ?>
        <img src="<?php echo $feature_img; ?>" alt="Feature Sentiment Graph">
    <?php else: ?>
        <p style="color:red;">Feature sentiment graph not found.</p>
    <?php endif; ?>

</div>

</body>
</html>
