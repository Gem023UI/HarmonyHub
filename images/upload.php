<?php
include('../style/config.php');

if (isset($_POST['upload_image'])) {
    $product_id = $_POST['product_id'];
    $upload_dir = '../media/products/';
    $file_name = basename($_FILES['product_image']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("UPDATE product SET product_image = ? WHERE product_id = ?");
        $stmt->bind_param("si", $target_file, $product_id);
        if ($stmt->execute()) {
            echo "Image uploaded successfully.";
        } else {
            echo "Database update failed.";
        }
    } else {
        echo "File upload failed.";
    }
    header("Location: products.php");
    exit();
}
?>
