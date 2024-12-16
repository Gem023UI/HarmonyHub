<?php
session_start();
include('../user/config.php'); // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);

    // Correct table name, assuming you want to delete from `cart`
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
    $stmt->bind_param("i", $cart_id);

    if ($stmt->execute()) {
        echo "Item successfully deleted.";
    } else {
        echo "Error deleting item: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header('Location: cartinterface.php'); // Redirect back to the cart view
    exit();
}
?>