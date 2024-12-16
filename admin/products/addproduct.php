<?php
session_start();
include('../../style/config.php');

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Redirect unauthorized users to an error page or login page
    header("Location: ../../user/login.php"); // Replace 'error.php' with the actual error page
    exit();
}

// Ensure the request is a POST request and has the required data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'], $_POST['stock'], $_POST['price'], $_POST['category_id'], $_FILES['product_image'])) {
    $description = $_POST['description'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $product_image = $_FILES['product_image']['name'];

    // Handle product image upload
    if (!empty($product_image)) {
        $target_dir = "../../media/products/";
        $target_file = $target_dir . basename($product_image);
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            // Insert product data into the database
            $sql = "INSERT INTO product (description, stock, price, category_id, product_image) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sddss", $description, $stock, $price, $category_id, $product_image);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Product added successfully!';
            } else {
                $_SESSION['error'] = 'Error adding product to the database.';
            }
        } else {
            $_SESSION['error'] = 'Error uploading product image.';
        }
    } else {
        $_SESSION['error'] = 'Product image is required.';
    }

    // Redirect back to the referring page or a default page if no referrer exists
    $referrer = $_SERVER['HTTP_REFERER'] ?? 'manageproducts.php';
    header("Location: $referrer");
    exit();
}

// Redirect to a default page if accessed improperly
header("Location: manageproducts.php");
exit();
?>
