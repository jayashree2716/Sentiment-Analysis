<?php
$product_id = $_GET['product_id'];

// RUN PYTHON SCRIPT
$cmd = "python sentiment_analysis.py $product_id";
shell_exec($cmd);
?>

<h2>Feature-wise Sentiment Graph</h2>
<img alt="Feature Graph"
     src="http://localhost/ecommerce/images/uploads/sentiment/feature_sentiment_product_<?php 
     echo $product_id; ?>.png?<?php echo time(); ?>"
     width="600">

<h2>Overall Sentiment Graph</h2>
<img alt="Overall Graph"
     src="http://localhost/ecommerce/images/uploads/sentiment/overall_sentiment_product_<?php 
     echo $product_id; ?>.png?<?php echo time(); ?>"
     width="600">
