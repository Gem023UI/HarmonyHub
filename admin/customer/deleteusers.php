<?php
include('../../style/config.php');
session_start();

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Redirect unauthorized users to an error page or login page
    header("Location: ../../user/login.php"); // Replace 'error.php' with the actual error page
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];

    // Delete the customer
    $sql = "DELETE FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $customer_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Customer deleted successfully!';
    } else {
        $_SESSION['error'] = 'Error deleting customer.';
    }

    $stmt->close();
    $conn->close();

    header("Location: manageusers.php");
    exit;
}
?>
