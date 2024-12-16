<?php
session_start();

include('../../style/categoryheader.php');
include('../../user/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['error'] = 'Please log in first';
    header("Location: login.php");
    exit();
}

// Dynamically fetch category ID (or default to 1)
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 1;

// Fetch products for the specified category
$sql = "SELECT p.product_id, p.description, p.price, p.product_image, c.description AS category_name 
        FROM product p 
        LEFT JOIN category c ON p.category_id = c.category_id 
        WHERE p.category_id = ? 
        ORDER BY p.product_id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$results = $stmt->get_result();

// Link the external CSS file
echo '<link rel="stylesheet" href="../../design/store/categories.css">';

if ($results && $results->num_rows > 0) {
    echo '<div class="product-container">';
    while ($row = $results->fetch_assoc()) {
        $product_id = $row['product_id'];
        $description = htmlspecialchars($row['description']);
        $price = number_format($row['price'], 2);
        $product_image = !empty($row['product_image']) ? "../../media/products/" . basename($row['product_image']) : '../../media/products/default.png';

        echo <<<HTML
        <div class="product-item">
            <div class="product-thumb">
                <img src="{$product_image}" alt="Product Image" class="product-image">
            </div>
            <div class="product-details">
                <h4>{$description}</h4>
                <p class="price">PHP {$price}</p>
                <form method="POST" action="../../cart/addtocart.php">
                    <input type="hidden" name="product_id" value="{$product_id}">
                    <input type="hidden" name="description" value="{$description}">
                    <input type="hidden" name="price" value="{$price}">
                    <input type="hidden" name="product_image" value="{$product_image}">
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" required>
                    <div class="add-to-cart-container">
                        <button type="submit" name="add_to_cart" class="add-to-cart-button">ADD TO CART</button>
                    </div>
                </form>

                <!-- Button to View Reviews -->
                <div class="view-reviews-container">
                    <a href="view_reviews.php?product_id={$product_id}" class="view-reviews-button">View Reviews</a>
                </div>
            </div>
        </div>
HTML;
    }
    echo '</div>';
} else {
    echo '<p>No products found for this category.</p>';
}
?>
