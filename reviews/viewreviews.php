<?php
session_start();
include('../style/reviewheader.php');
include('../user/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['error'] = 'Please log in first';
    header("Location: login.php");
    exit();
}

// Get product ID from the URL
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Fetch product details
$product_query = "SELECT p.product_id, p.description, p.product_image 
                  FROM product p WHERE p.product_id = ?";
$product_stmt = $conn->prepare($product_query);
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
    $product_description = htmlspecialchars($product['description']);
    $product_image = !empty($product['product_image']) ? "../../media/products/" . basename($product['product_image']) : '../../media/products/default.png';
} else {
    echo "<p>Product not found.</p>";
    exit();
}

// Fetch reviews for the product
$review_query = "SELECT r.review_text, r.rating, r.created_at, c.firstname, c.lastname 
                 FROM reviews r 
                 JOIN customer c ON r.customer_id = c.customer_id
                 WHERE r.product_id = ?
                 ORDER BY r.created_at DESC";
$review_stmt = $conn->prepare($review_query);
$review_stmt->bind_param("i", $product_id);
$review_stmt->execute();
$reviews_result = $review_stmt->get_result();

// Link the external CSS file
echo '<link rel="stylesheet" href="../../design/store/view_reviews.css">';

echo "<div class='product-details-container'>
        <div class='product-info'>
            <img src='{$product_image}' alt='Product Image' class='product-image'>
            <h2>{$product_description}</h2>
        </div>
        <h3>Reviews</h3>";

if ($reviews_result && $reviews_result->num_rows > 0) {
    while ($review = $reviews_result->fetch_assoc()) {
        $review_text = htmlspecialchars($review['review_text']);
        $rating = $review['rating'];
        $created_at = date('F j, Y', strtotime($review['created_at']));
        $customer_name = htmlspecialchars($review['firstname']) . ' ' . htmlspecialchars($review['lastname']);

        echo "<div class='review'>
                <p><strong>{$customer_name}</strong> ({$created_at}) - Rating: {$rating}/5</p>
                <p>{$review_text}</p>
              </div>";
    }
} else {
    echo "<p>No reviews yet for this product.</p>";
}

echo "</div>";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .product-details-container {
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .product-info {
            display: flex;
            align-items: center;
        }

        .product-info img {
            width: 150px;
            height: auto;
            margin-right: 20px;
        }

        .product-info h2 {
            margin: 0;
        }

        .review {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .review p {
            margin: 5px 0;
        }

        .review strong {
            color: #2c3e50;
        }
    </style>
</head>
<body>
</body>
</html>
