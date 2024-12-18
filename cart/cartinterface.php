<?php
session_start();
include('../style/cartheader.php');
include('../user/config.php'); // Include database connection

// Ensure the user is logged in
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['error'] = 'Please log in first';
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id']; // Get the logged-in customer's ID

// Fetch cart items for the current customer
$stmt = $conn->prepare("
    SELECT c.cart_id, c.description, c.price, c.product_image, c.quantity 
    FROM cart c
    WHERE c.customer_id = ?
");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$cart_items = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart</title>
    <link rel="stylesheet" href="../design/cart/cart.css">
</head>
<body>
    <div class="cart-container">
        <h2>CART PRODUCTS</h2>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>SELECT</th>
                    <th>DESCRIPTION</th>
                    <th>PRICE</th>
                    <th>ITEM IMAGE</th>
                    <th>QUANTITY</th>
                    <th>MANAGE</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($cart_items->num_rows > 0): ?>
                    <?php while ($item = $cart_items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="cart_id[]" value="<?php echo $item['cart_id']; ?>" class="cart-checkbox" 
                                    data-price="<?php echo htmlspecialchars($item['price']); ?>" 
                                    onclick="handleCheckboxClick(this, <?php echo $item['cart_id']; ?>)">
                            </td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td>PHP <?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <img src="../media/products/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                    alt="Product Image" width="100" height="100">
                            </td>
                            <td>
                                <div class="quantity-control">
                                    <button type="button" onclick="adjustQuantity('<?php echo $item['cart_id']; ?>', -1)">-</button>
                                    <input type="number" id="quantity-<?php echo $item['cart_id']; ?>" 
                                        name="quantity-<?php echo $item['cart_id']; ?>" 
                                        value="<?php echo intval($item['quantity']); ?>" readonly>
                                    <button type="button" onclick="adjustQuantity('<?php echo $item['cart_id']; ?>', 1)">+</button>
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="deletecart.php" style="display:inline;">
                                    <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                    <button type="submit" name="delete_cart">DELETE</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="total-price-container">
            <p><strong>TOTAL PRICE :</strong> PHP <span id="total-price">0.00</span></p>
            <form method="POST" action="checkout.php">
                <!-- Add hidden input to pass the checked cart_ids -->
                <input type="hidden" name="cart_ids" value="" id="cart_ids_input">
                <button type="submit" class="proceed-to-checkout-btn">Proceed to Checkout</button>
            </form>
        </div>
    </div>

    <script>
        /**
         * Handle the checkbox click to update cart selection and recalculate the total.
         * @param {HTMLInputElement} checkbox - The clicked checkbox.
         * @param {number} cartId - The ID of the cart item.
         */
        function handleCheckboxClick(checkbox, cartId) {
            const isChecked = checkbox.checked ? 1 : 0;
            fetch('checkcart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `cart_id=${cartId}&checkbox=${isChecked}`
            }).then(response => response.text())
              .then(data => console.log(data))
              .catch(error => console.error('Error:', error));
            
            calculateTotal();
        }

        /**
         * Calculate the total price of selected items.
         */
        function calculateTotal() {
            let total = 0;
            const checkboxes = document.querySelectorAll('.cart-checkbox');

            checkboxes.forEach((checkbox) => {
                if (checkbox.checked) {
                    const row = checkbox.closest('tr');
                    const price = parseFloat(checkbox.dataset.price);
                    const quantityInput = row.querySelector('.quantity-control input');
                    const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
                    total += price * quantity;
                }
            });

            document.getElementById('total-price').innerText = total.toFixed(2);
        }

        /**
         * Adjust the quantity of items.
         */
        function adjustQuantity(id, change) {
            const quantityInput = document.getElementById(`quantity-${id}`);
            let currentValue = parseInt(quantityInput.value);
            currentValue += change;

            if (currentValue < 1) {
                currentValue = 1;
            }

            quantityInput.value = currentValue;

            fetch('editquantity.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `cart_id=${id}&quantity=${currentValue}`
            }).then(response => response.json())
              .then(data => {
                  if (data.status !== 'success') {
                      console.error('Error updating quantity:', data.message);
                  }
              }).catch(error => console.error('Error:', error));

            calculateTotal();
        }
    </script>
</body>
</html>
