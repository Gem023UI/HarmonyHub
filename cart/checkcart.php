<?php
session_start();
include('../user/config.php'); // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'], $_POST['checkbox'])) {
    $cart_id = intval($_POST['cart_id']);
    $checkbox = intval($_POST['checkbox']); // 1 for checked, 0 for unchecked

    $stmt = $conn->prepare("UPDATE cart SET checkbox = ? WHERE cart_id = ?");
    $stmt->bind_param('ii', $checkbox, $cart_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Cart item selection updated.']);
    } else {
        echo json_encode(['success' => false, 'message' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>
