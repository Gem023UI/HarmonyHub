<?php
session_start();
include('../user/config.php'); // Include your database connection file

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $cart_id = isset($_POST['cart_id']) ? intval($_POST['cart_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

    if ($cart_id > 0 && $quantity > 0) {
        // Update the cart quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
        $stmt->bind_param("ii", $quantity, $cart_id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Quantity updated successfully.';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Failed to update quantity.';
        }
        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Invalid cart ID or quantity.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method.';
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
