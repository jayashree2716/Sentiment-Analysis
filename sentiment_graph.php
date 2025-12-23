
<?php 
$product_id = $_GET['product_id'];

// FULL PATHS
$python = "C:\\Python313\\python.exe";
$script = "C:\\wamp64\\www\\ecommerce\\python\\sentiment_analysis.py";
$script = "C:\\wamp64\\www\\ecommerce\\python\\sentiment_analysis_llm.py";


// BUILD COMMAND
$command = "$python $script $product_id";

// RUN PYTHON
shell_exec($command);
?>



<!DOCTYPE html>
<html>
<head>
<title>Sentiment Analysis Graph</title>
<style>
    body { font-family: Arial; margin: 20px; }
    img { width: 600px; margin-bottom: 25px; border:1px solid #ccc; }
</style>
</head>

<body>

<h2>Sentiment Analysis for Product ID: <?php echo $product_id; ?></h2>

<?php
$overall_img = "http://localhost/ecommerce/images/uploads/sentiment/overall_sentiment_product_" . $product_id . ".png";
$feature_img = "http://localhost/ecommerce/images/uploads/sentiment/feature_sentiment_product_" . $product_id . ".png";
?>

<!-- DISPLAY OVERALL SENTIMENT GRAPH -->
<h3>Overall Sentiment</h3>
<img src="<?php echo $overall_img; ?>" alt="Overall Sentiment Graph">

<!-- DISPLAY FEATURE-WISE SENTIMENT GRAPH -->
<h3>Feature-wise Sentiment</h3>
<img src="<?php echo $feature_img; ?>" alt="Feature Sentiment Graph">



</body>
</html>

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


<!--   llm -->  



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




<!-- logistic -->
<?php
$product_id = $_GET['id'];

// Run Python sentiment script
$cmd = "python sentiment_product.py $product_id";
$output = trim(shell_exec($cmd));

if ($output == "NO_PRODUCT") {
    echo "<h3>Product Not Found</h3>";
    exit;
}

if ($output == "NO_COMMENTS") {
    echo "<h3>No Comments Available for this Product</h3>";
    exit;
}
?>

<h2>Overall Sentiment</h2>
<img src="/ecommerce/images/uploads/sentiment/overall_sentiment.png" width="500">

<h2>Feature-wise Sentiment</h2>
<img src="/ecommerce/images/uploads/sentiment/feature_sentiment.png" width="700">

</body>
</html>

