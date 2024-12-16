<?php
include('../../style/config.php');
session_start();

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Redirect unauthorized users to an error page or login page
    header("Location: ../../user/login.php"); // Replace 'error.php' with the actual error page
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Begin a transaction
    $conn->begin_transaction();

    try {
        // Delete related rows in the cart table
        $sql_cart = "DELETE FROM cart WHERE product_id = ?";
        $stmt_cart = $conn->prepare($sql_cart);
        $stmt_cart->bind_param('i', $product_id);
        $stmt_cart->execute();
        $stmt_cart->close();

        // Delete the product
        $sql_product = "DELETE FROM product WHERE product_id = ?";
        $stmt_product = $conn->prepare($sql_product);
        $stmt_product->bind_param('i', $product_id);
        $stmt_product->execute();
        $stmt_product->close();

        // Commit the transaction
        $conn->commit();

        $_SESSION['success'] = 'Product deleted successfully!';
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        $_SESSION['error'] = 'Error deleting product: ' . $e->getMessage();
    }

    header("Location: manageproducts.php");
    exit();
}
?>
