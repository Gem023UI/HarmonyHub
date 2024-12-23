<?php
session_start();
include('../style/orderheader.php');
include('../user/config.php');  // Include the database connection

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Debug to confirm if orderinfo_id is being received
if (!isset($_GET['orderinfo_id']) || empty($_GET['orderinfo_id'])) {
    echo "<p>No order ID provided. Please select an order to view its details.</p>";
    exit();
}

$orderinfo_id = $_GET['orderinfo_id'];

// Fetch order details along with the status description for the customer
$order_query = "SELECT oi.orderinfo_id, oi.date_placed, os.description AS status_description
                FROM orderinfo oi
                JOIN order_status os ON oi.status_id = os.os_id
                WHERE oi.orderinfo_id = ? AND oi.customer_id = ?";
$order_stmt = $conn->prepare($order_query);
$order_stmt->bind_param('ii', $orderinfo_id, $customer_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

// Fetch product details for the order
$product_query = "SELECT oline.prod_id, p.description, oline.quantity, p.price
                  FROM orderline oline
                  JOIN product p ON oline.prod_id = p.product_id
                  WHERE oline.orderinfo_id = ?";
$product_stmt = $conn->prepare($product_query);
$product_stmt->bind_param('i', $orderinfo_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($order_result->num_rows > 0) {
    $order_details = $order_result->fetch_assoc();  // Get order details
} else {
    echo "<p>Order not found or does not belong to you.</p>";
    exit();
}

$product_details = [];
while ($item = $product_result->fetch_assoc()) {
    $product_details[] = $item; // Collect product details for the order
}

$conn->close();
?>

<!-- HTML Body -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../design/store/orderinterface.css">
</head>
<body>
    <div class="container">
        <div class="order-item">
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order_details['orderinfo_id']); ?></p>
            <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_details['date_placed']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order_details['status_description']); ?></p> <!-- Now showing status description -->
        </div>

        <div class="product-list">
            <?php
            $total_price = 0; // Initialize total price
            foreach ($product_details as $item):
                $product_total = $item['price'] * $item['quantity']; // Calculate total for this product
                $total_price += $product_total; // Add to the total price
            ?>
            <div class="product-item">
                <p><strong>Product Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($item['quantity']); ?></p>
                <p><strong>Unit Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
                <p><strong>Total Price:</strong> $<?php echo number_format($product_total, 2); ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($order_details['status_description'] === 'received'): ?>
            <div class="action-button">
                <button onclick="location.href='../reviews/viewreviews.php?orderinfo_id=<?php echo $orderinfo_id; ?>'">
                    ADD REVIEW
                </button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
