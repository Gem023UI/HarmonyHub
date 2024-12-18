<?php
session_start();
include('../style/orderheader.php');
include('../user/config.php'); // Include database connection

// Function to fetch customer details
function getCustomerDetails($conn, $customer_id) {
    $query = "SELECT firstname, lastname, email, city FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Function to fetch cart item details
function getCartItemDetails($conn, $cart_id) {
    $query = "SELECT c.cart_id, c.quantity, p.description, p.price, p.product_id 
              FROM cart c 
              JOIN product p ON c.product_id = p.product_id 
              WHERE c.cart_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $cart_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch customer details from database
$customer_id = $_SESSION['customer_id'];
$customer_info = getCustomerDetails($conn, $customer_id);

// Initialize variables for order confirmation
$orderinfo_id = null;
$total_price = 0;
$order_success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_ids'])) {
    $cart_ids = explode(',', $_POST['cart_ids']);

    // Insert into orderinfo
    $query = "INSERT INTO orderinfo (customer_id, date_placed, status_id) VALUES (?, NOW(), 1)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $orderinfo_id = $stmt->insert_id;

    // Loop through cart items to insert into orderline
    foreach ($cart_ids as $cart_id) {
        $item = getCartItemDetails($conn, $cart_id);

        if ($item) {
            $query = "INSERT INTO orderline (orderinfo_id, prod_id, quantity) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iii', $orderinfo_id, $item['product_id'], $item['quantity']);
            $stmt->execute();

            $total_price += $item['price'] * $item['quantity'];
        } else {
            echo "Error: Cart item with ID $cart_id not found.<br>";
        }
    }

    // Mark the order as successful
    $order_success = true;

    // Update cart items' checkbox to unchecked
    $stmt = $conn->prepare("UPDATE cart SET checkbox = 0 WHERE cart_id IN (" . implode(',', array_fill(0, count($cart_ids), '?')) . ")");
    $stmt->bind_param(str_repeat('i', count($cart_ids)), ...$cart_ids);
    $stmt->execute();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../style.css"> <!-- Assuming you have a common stylesheet -->
    <link rel="stylesheet" href="../design/store/orderinterface.css">  <!-- Link to the new CSS file -->
</head>
<body>
    <!-- Your other HTML content, such as header, menu, etc. -->

    <?php if ($order_success): ?>
        <div class="order-confirmation">
            <h2>Order Placed Successfully!</h2>
            <p>Your order has been placed for <strong><?php echo count($cart_ids); ?></strong> item(s).</p>
            <p>Total Price: <strong>₱<?php echo number_format($total_price, 2); ?></strong></p>
            <p>Thank you for shopping with us!</p>
            <a href="../orders/orderinterface.php?orderinfo_id=<?php echo $orderinfo_id; ?>">View Order History</a>
        </div>
    <?php endif; ?>

    <!-- Your other HTML content, such as footer, etc. -->

</body>
</html>
