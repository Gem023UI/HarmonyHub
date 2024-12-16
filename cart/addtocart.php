<?php
session_start();
include('../user/config.php'); // Include database connection

// Check if the "Add to Cart" button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Ensure the user is logged in
    if (!isset($_SESSION['customer_id'])) {
        header("Location: /login.php"); // Redirect to login if not logged in
        exit();
    }

    // Retrieve customer ID from the session
    $customer_id = intval($_SESSION['customer_id']);

    // Retrieve product details from the POST request
    $product_id = intval($_POST['product_id']);
    $description = $_POST['description'];
    $product_image = $_POST['product_image'];
    $quantity = intval($_POST['quantity']);

    // Fetch the actual price and stock from the `product` table to ensure precision
    $stmt = $conn->prepare("SELECT price, stock FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($price, $stock);
    $stmt->fetch();
    $stmt->close();

    // Check stock availability
    if ($stock !== null && $stock >= $quantity) {
        // Insert into the `cart` table
        $stmt = $conn->prepare("INSERT INTO cart (customer_id, product_id, description, price, product_image, quantity) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdsi", $customer_id, $product_id, $description, $price, $product_image, $quantity);
        $stmt->execute();
        $stmt->close();
    } else {
        // If stock is insufficient, redirect with an error message
        $_SESSION['error'] = "Insufficient stock for the selected product.";
    }

    // Redirect back to the referring page
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit();
}

// Close the database connection
$conn->close();
?>
