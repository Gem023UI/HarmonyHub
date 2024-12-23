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
$product_id = isset($_GET['prod_id']) ? intval($_GET['prod_id']) : 0;

if ($product_id === 0) {
    echo "<p>Invalid product ID.</p>";
    exit();
}

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
    $product_image = !empty($product['product_image']) ? "../media/products/" . basename($product['product_image']) : '../../media/products/default.png';
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

$reviews = [];
if ($reviews_result && $reviews_result->num_rows > 0) {
    while ($review = $reviews_result->fetch_assoc()) {
        $reviews[] = [
            'text' => htmlspecialchars($review['review_text']),
            'rating' => $review['rating'],
            'date' => date('F j, Y', strtotime($review['created_at'])),
            'customer' => htmlspecialchars($review['firstname']) . ' ' . htmlspecialchars($review['lastname']),
        ];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Reviews</title>
    <link rel="stylesheet" href="../design/store/reviewinterface.css">
</head>
<body>
    <div class="product-details-container">
        <div class="product-info">
            <img src="<?php echo $product_image; ?>" alt="Product Image" class="product-image">
            <h2><?php echo $product_description; ?></h2>

            <!-- Insert Review Button -->
            <form action="addreview.php" method="post">
                <input type="hidden" name="prod_id" value="<?php echo $product_id; ?>">
                <button type="submit" class="insert-review-btn">Insert Review</button>
            </form>
        </div>

        <h3>Reviews</h3>
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <p><strong><?php echo $review['customer']; ?></strong> (<?php echo $review['date']; ?>) - Rating: <?php echo $review['rating']; ?>/5</p>
                    <p><?php echo $review['text']; ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No reviews yet for this product.</p>
        <?php endif; ?>
    </div>
</body>
</html>
