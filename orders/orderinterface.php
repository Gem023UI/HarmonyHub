<?php
session_start();
include('../style/orderheader.php');
include('../user/config.php');

// Function to fetch all orders
function getAllOrders($conn) {
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
        ORDER BY 
            oi.date_placed DESC
    ";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Fetch all orders from the database
$order_history = getAllOrders($conn);

// If no orders found
if (empty($order_history)) {
    echo "<p>No orders found in the system.</p>";
} else {
    echo "<h2>All Orders</h2>";

    // Display all orders
    foreach ($order_history as $order) {
        echo "
        <div class='order-item'>
            <p><strong>Order ID:</strong> " . htmlspecialchars($order['order_id']) . "</p>
            <p><strong>Customer Name:</strong> " . htmlspecialchars($order['firstname']) . " " . htmlspecialchars($order['lastname']) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($order['email']) . "</p>
            <p><strong>City:</strong> " . htmlspecialchars($order['city']) . "</p>
            <p><strong>Order Date:</strong> " . htmlspecialchars($order['created_at']) . "</p>
            <p><strong>Product Description:</strong> " . htmlspecialchars($order['description']) . "</p>
            <p><strong>Quantity:</strong> " . $order['quantity'] . "</p>
            <p><strong>Price:</strong> ₱" . number_format($order['price'], 2) . "</p>
            <p><strong>Status:</strong> " . htmlspecialchars($order['status']) . "</p>
            <a class='view-order-btn' href='vieworder.php?orderinfo_id=" . $order['order_id'] . "'>View Order Details</a>
        </div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #2c3e50;
        }

        .order-item {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 900px;
        }

        .order-item p {
            margin: 10px 0;
            font-size: 16px;
        }

        .order-item p strong {
            color: #2c3e50;
        }

        .view-order-btn {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            margin-top: 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .view-order-btn:hover {
            background-color: #2980b9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .order-item {
                width: 90%;
                padding: 15px;
            }

            .view-order-btn {
                width: 100%;
                padding: 12px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Your PHP content will appear here -->
</body>
</html>
