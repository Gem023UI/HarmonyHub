<?php
session_start();
include('../user/config.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editcart'])) {
    $cart_id = intval($_POST['cart_id']); // Cart ID from hidden input
    $product_id = intval($_POST['product_id']); // New Product ID from dropdown

    if ($cart_id > 0 && $product_id > 0) {
        // Fetch the description, price, and product_image for the selected product
        $stmt = $conn->prepare("SELECT description, price, product_image FROM product WHERE product_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            $stmt->close();

            if ($product) {
                $description = $product['description'];
                $price = $product['price'];
                $product_image = $product['product_image'];

                // Update the cart table with the new product details
                $stmt = $conn->prepare("UPDATE cart SET description = ?, price = ?, product_image = ? WHERE cart_id = ?");
                if ($stmt) {
                    $stmt->bind_param("sdsi", $description, $price, $product_image, $cart_id);
                    if ($stmt->execute()) {
                        $_SESSION['success_message'] = "Cart updated successfully!";
                    } else {
                        $_SESSION['error_message'] = "Failed to update cart. Please try again.";
                    }
                    $stmt->close();
                } else {
                    $_SESSION['error_message'] = "Error preparing the update query.";
                }
            } else {
                $_SESSION['error_message'] = "Selected product not found.";
            }
        } else {
            $_SESSION['error_message'] = "Error preparing the fetch query.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid input. Please try again.";
    }

    header("Location: cartinterface.php"); // Redirect back to the cart page
    exit();
} else {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: cartinterface.php");
    exit();
}

$conn->close(); // Close database connection
?>
