<?php
session_start();
include('../style/cartheader.php');
include('../user/config.php'); // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

// Force database to retrieve the most updated data
$conn->query("SET TRANSACTION ISOLATION LEVEL READ COMMITTED");

// Fetch customer details from the customer table
$customer_id = $_SESSION['customer_id'];
$query = "SELECT firstname, lastname, email, city FROM customer WHERE customer_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$customer_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch selected cart items (checkbox = 1) from the cart table
$query = "SELECT c.cart_id, c.product_id, c.quantity, c.checkbox, 
                 p.description AS product_description, 
                 p.price AS product_price, 
                 p.product_image
          FROM cart c
          JOIN product p ON c.product_id = p.product_id
          WHERE c.customer_id = ? AND c.checkbox = 1"; // Fetch only items with checkbox = 1
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $customer_id);
$stmt->execute();
$cart_items_result = $stmt->get_result();
$stmt->close();

$redirect_url = '../orders/placeorder.php'; // Adjust path if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../design/cart/checkout.css">
</head>
<body>
    <div class="checkout-container">
        <!-- Customer Information Container -->
        <div class="customer-info-container">
            <h2>Customer Information</h2>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($customer_info['firstname']); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($customer_info['lastname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($customer_info['email']); ?></p>
            <p><strong>City:</strong> <?php echo htmlspecialchars($customer_info['city']); ?></p>
        </div>

        <hr>

        <!-- Cart Items Container -->
        <div class="cart-items-container">
            <h2>Cart Items</h2>
            <?php if ($cart_items_result->num_rows > 0): ?>
                <table border="1" cellpadding="10" cellspacing="0">
                    <thead>
                        <tr>
                            <th>DESCRIPTION</th>
                            <th>PRICE</th>
                            <th>QUANTITY</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_price = 0;
                        $cart_ids = []; // Collect cart IDs for the form
                        while ($item = $cart_items_result->fetch_assoc()) {
                            // Fetch correct product details for each cart item
                            $description = htmlspecialchars($item['product_description']);
                            $price = is_numeric($item['product_price']) ? $item['product_price'] : 0;
                            $quantity = is_numeric($item['quantity']) ? $item['quantity'] : 0;
                            $item_total = $price * $quantity;
                            $cart_ids[] = $item['cart_id'];
                            $total_price += $item_total;
                        ?>
                            <tr>
                                <td><?php echo $description; ?></td>
                                <td>PHP <?php echo number_format($price, 2); ?></td>
                                <td><?php echo $quantity; ?></td>
                                <td>PHP <?php echo number_format($item_total, 2); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <hr>

                <div class="total-price-container">
                    <p><strong>Total Price:</strong> PHP <?php echo number_format($total_price, 2); ?></p>
                </div>
            <?php else: ?>
                <p>No items selected for checkout. Please <a href="cartinterface.php">return to the cart</a> and select items to checkout.</p>
            <?php endif; ?>
        </div>

        <hr>

        <!-- Checkout Form -->
        <form method="POST" action="<?php echo htmlspecialchars($redirect_url); ?>">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <input type="hidden" name="cart_ids" value="<?php echo implode(',', $cart_ids); ?>">
            <button type="submit">Place Order</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
