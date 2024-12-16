<?php
include('../../style/config.php');

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Redirect unauthorized users to an error page or login page
    header("Location: ../../user/login.php"); // Replace 'error.php' with the actual error page
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $product_image = $_FILES['product_image']['name'];

    // Handle product image upload
    if (!empty($product_image)) {
        $target_dir = "../../media/products/";
        $target_file = $target_dir . basename($product_image);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);
    }

    $sql = "UPDATE product SET description = ?, price = ?, stock = ?, category_id = ?, product_image = ? WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddssi", $description, $price, $stock, $category_id, $product_image, $product_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Product updated successfully!';
    } else {
        $_SESSION['error'] = 'Error updating product.';
    }

    header("Location: manageproducts.php");
}
?>
