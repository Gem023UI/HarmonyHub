<?php
session_start();
include('../style/orderheader.php');
include('../user/config.php');

// Ensure the customer_id is set in the session (you can set it upon login)
if (!isset($_SESSION['customer_id'])) {
    // Redirect to login or show an error if no customer_id is in the session
    echo "You must be logged in to view your order history.";
    exit;
}

// Get the customer_id from the session
$customer_id = $_SESSION['customer_id'];

// Function to fetch orders for the current customer
function getCustomerOrders($conn, $customer_id) {
    $query = "
        SELECT 
            oi.orderinfo_id AS order_id,
            c.firstname,
            c.lastname,
            c.email,
            c.city,
            p.description,
            ol.quantity,
            p.price,
            oi.date_placed AS created_at,
            os.description AS status
        FROM 
            orderinfo oi
        JOIN 
            customer c ON oi.customer_id = c.customer_id
        JOIN 
            orderline ol ON oi.orderinfo_id = ol.orderinfo_id
        JOIN 
            product p ON ol.prod_id = p.product_id
        JOIN 
            order_status os ON oi.status_id = os.os_id
        WHERE 
            oi.customer_id = ?  -- Only get orders for the logged-in customer
        ORDER BY 
            oi.date_placed DESC
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    // Bind the customer_id to the prepared statement
    $stmt->bind_param("i", $customer_id);

    // Execute the statement
    $stmt->execute();

    // Fetch all results
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch the orders for the current customer
$order_history = getCustomerOrders($conn, $customer_id);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="../design/store/orderinterface.css"> 
</head>
<body>
    <div class="order-history-container">
        <h2>Your Order History</h2>

        <?php if (empty($order_history)): ?>
            <p>You have not placed any orders yet.</p>
        <?php else: ?>
            <?php foreach ($order_history as $order): ?>
                <div class="order-item">
                    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?></p>
                    <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['firstname']) . " " . htmlspecialchars($order['lastname']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                    <p><strong>City:</strong> <?php echo htmlspecialchars($order['city']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                    <p><strong>Product Description:</strong> <?php echo htmlspecialchars($order['description']); ?></p>
                    <p><strong>Quantity:</strong> <?php echo $order['quantity']; ?></p>
                    <p><strong>Price:</strong> ₱<?php echo number_format($order['price'], 2); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <a class="view-order-btn" href="vieworder.php?orderinfo_id=<?php echo $order['order_id']; ?>">View Order Details</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
