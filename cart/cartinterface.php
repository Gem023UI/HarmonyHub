    <?php
    session_start();
    include('../style/cartheader.php');
    include('../user/config.php'); // Include database connection

    $current_item = null;
    $same_category_items = [];

    // Fetch all cart items
    $stmt = $conn->prepare("SELECT c.cart_id, c.description, c.price, c.product_image, c.quantity 
                            FROM cart c");
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
                    <?php while ($item = $cart_items->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <input type="checkbox" name="cart_id[]" value="<?php echo $item['cart_id']; ?>" class="cart-checkbox" 
                                    data-price="<?php echo htmlspecialchars($item['price']); ?>" 
                                    onclick="handleCheckboxClick(this, <?php echo $item['cart_id']; ?>)">
                            </td>
                            <td><?php echo htmlspecialchars($item['description']); ?></td>
                            <td>PHP <?php echo number_format($item['price']); ?></td>
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

        <?php if ($current_item): ?>
            <div class="modal-overlay" style="display: block;"></div>
            <div class="modal" style="display: block;">
                <div class="edit-cart-content">
                    <div class="image-section">
                        <img src="../media/products/<?php echo htmlspecialchars($current_item['product_image']); ?>" alt="Product Image">
                    </div>
                    <div class="details-section">
                        <p><strong>PRODUCT:</strong> <?php echo htmlspecialchars($current_item['description']); ?></p>
                        <p><strong>PRICE:</strong> PHP <?php echo number_format($current_item['price'], 2); ?></p>
                        <form method="POST" action="editcart.php">
                            <input type="hidden" name="cart_id" value="<?php echo $current_item['cart_id']; ?>">
                            <label for="other-products">Choose New Product:</label>
                            <select name="product_id" required>
                                <option value="" disabled selected>Select another product</option>
                                <?php foreach ($same_category_items as $item): ?>
                                    <option value="<?php echo $item['product_id']; ?>">
                                        <?php echo htmlspecialchars($item['description']); ?> - PHP <?php echo number_format($item['price'], 2); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="editcart" class="save-btn">UPDATE</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

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
                        const row = checkbox.closest('tr'); // Get the row containing the checkbox
                        const price = parseFloat(checkbox.dataset.price); // Get the item's price
                        const quantityInput = row.querySelector('.quantity-control input');
                        const quantity = quantityInput ? parseInt(quantityInput.value) : 1; // Default to 1 if input missing
                        total += price * quantity; // Multiply price by quantity
                    }
                });

                // Update the total price display
                document.getElementById('total-price').innerText = total.toFixed(2);
            }

            /**
             * Update the cart selection in the database.
             * @param {HTMLInputElement} checkbox - The clicked checkbox.
             * @param {number} cartId - The ID of the cart item.
             */
            function updateCartSelection(checkbox, cartId) {
                const isSelected = checkbox.checked ? 1 : 0;  // 1 if checked, 0 if unchecked

                // Make an AJAX request to update the cart selection
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'storechecked.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                // On successful completion of the AJAX request
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText); // You can log or alert the response for debugging
                    }
                };

                // Send the cartId and selected status to the PHP script
                xhr.send('cart_id=' + cartId + '&checkbox=' + isSelected);  // Send the cart ID and checkbox selection state
            }

            /**
             * Adjust the quantity of items.
             * Reused from previous implementation.
             */
            function adjustQuantity(id, change) {
                const quantityInput = document.getElementById(`quantity-${id}`);
                let currentValue = parseInt(quantityInput.value);
                currentValue += change;

                if (currentValue < 1) {
                    currentValue = 1; // Minimum quantity is 1
                }

                quantityInput.value = currentValue;

                // Update the server
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'editquantity.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            const response = JSON.parse(xhr.responseText);
                            if (response.status === 'success') {
                                console.log('Quantity updated successfully');
                            } else {
                                console.error('Error updating quantity: ' + response.message);
                            }
                        } else {
                            console.error('Server error');
                        }
                    }
                };
                xhr.send(`cart_id=${id}&quantity=${currentValue}`);

                // Recalculate total price
                calculateTotal();
            }

        </script>
    </body>
    </html>