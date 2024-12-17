<?php
ob_start(); // Start output buffering
session_start();
include('../style/storeheader.php');
include('../user/config.php');  // Include the database connection

// Function to insert a review into the reviews table
function addReview($conn, $orderinfo_id, $customer_id, $product_id, $review_text, $rating) {
    $forbidden_words = ['gago', 'tanga', 'tanginamo', 'hayop', 'tarantado', 'puta', 'potangina', 'putangina', 'kupal', 'fuck', 'tangina', 'shit'];

    foreach ($forbidden_words as $word) {
        $review_text = preg_replace("/\b" . preg_quote($word, '/') . "\b/i", '****', $review_text);
    }

    $query = "INSERT INTO reviews (orderinfo_id, customer_id, product_id, review_text, rating, created_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo "<p>Database error: " . htmlspecialchars($conn->error) . "</p>";
        return false;
    }
    $stmt->bind_param('iiisi', $orderinfo_id, $customer_id, $product_id, $review_text, $rating);
    return $stmt->execute();
}

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Debug to confirm if orderinfo_id is being received
if (!isset($_GET['orderinfo_id']) || empty($_GET['orderinfo_id'])) {
    echo "<p>No order ID provided. Please select an order to leave a review.</p>";
    echo "<p>Debug: Missing or empty `orderinfo_id` in the URL.</p>";
    echo "<p>Example link: <code>order_review.php?orderinfo_id=123</code></p>";
    exit();
}

$orderinfo_id = $_GET['orderinfo_id'];

// Fetch order details for the customer
$query = "SELECT oi.orderinfo_id, oi.date_placed, oline.prod_id, p.description, oline.quantity
          FROM orderinfo oi
          JOIN orderline oline ON oi.orderinfo_id = oline.orderinfo_id
          JOIN product p ON oline.prod_id = p.product_id
          WHERE oi.orderinfo_id = ? AND oi.customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $orderinfo_id, $customer_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows > 0) {
    $order = $order_result->fetch_assoc();

    echo "
    <div class='order-item'>
        <p><strong>Order ID:</strong> " . htmlspecialchars($order['orderinfo_id']) . "</p>
        <p><strong>Order Date:</strong> " . htmlspecialchars($order['date_placed']) . "</p>
    </div>";

    foreach ($order_result as $item) {
        echo "
        <div class='product-item'>
            <p><strong>Product Description:</strong> " . htmlspecialchars($item['description']) . "</p>
            <p><strong>Quantity:</strong> " . htmlspecialchars($item['quantity']) . "</p>
        </div>";
    }

    echo "
    <form method='POST' class='review-form'>
        <input type='hidden' name='orderinfo_id' value='" . htmlspecialchars($orderinfo_id) . "'>
        <input type='hidden' name='product_id' value='" . htmlspecialchars($order['prod_id']) . "'>
        <textarea name='review_text' placeholder='Write your review here...' required></textarea><br>
        <label for='rating'>Rating (1 to 5): </label>
        <input type='number' name='rating' min='1' max='5' required><br>
        <button type='submit'>Submit Review</button>
    </form>";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['review_text'], $_POST['rating'], $_POST['orderinfo_id'], $_POST['product_id'])) {
        $review_text = $_POST['review_text'];
        $rating = (int)$_POST['rating'];
        $product_id = (int)$_POST['product_id'];
        $orderinfo_id = (int)$_POST['orderinfo_id'];

        if (addReview($conn, $orderinfo_id, $customer_id, $product_id, $review_text, $rating)) {
            header("Location: ../orders/viewreviews.php?product_id=" . $product_id);
            exit();
        } else {
            echo "<p>There was an error submitting your review. Please try again later.</p>";
        }
    }
} else {
    echo "<p>Order not found or does not belong to you.</p>";
}

$conn->close();
ob_end_flush();
?>


<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .order-item, .product-item {
        background-color: #fff;
        padding: 20px;
        margin: 10px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .order-item p, .product-item p {
        font-size: 16px;
        line-height: 1.5;
    }

    .review-form {
        background-color: #fff;
        padding: 20px;
        margin: 20px 0;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .review-form textarea {
        width: 100%;
        height: 100px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        resize: none;
    }

    .review-form input[type="number"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .review-form button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    .review-form button:hover {
        background-color: #45a049;
    }

    label {
        font-size: 16px;
        margin-top: 10px;
        display: block;
    }
</style>
