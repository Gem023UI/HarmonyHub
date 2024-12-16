<?php
include('../style/config.php');

if (isset($_POST['delete_image'])) {
    $product_id = $_POST['product_id'];

    $stmt = $conn->prepare("SELECT product_image FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($product_image);
    $stmt->fetch();
    $stmt->close();

    if ($product_image && file_exists($product_image)) {
        unlink($product_image);
    }

    $default_image = '../media/products/default_product.png';
    $stmt = $conn->prepare("UPDATE product SET product_image = ? WHERE product_id = ?");
    $stmt->bind_param("si", $default_image, $product_id);
    if ($stmt->execute()) {
        echo "Image deleted successfully.";
    } else {
        echo "Database update failed.";
    }

    header("Location: products.php");
    exit();
}
?>
