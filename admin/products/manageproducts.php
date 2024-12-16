<?php
session_start();
include('../../style/config.php');

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Redirect unauthorized users to an error page or login page
    header("Location: ../../user/login.php"); // Replace 'error.php' with the actual error page
    exit();
}

// Fetch all products from the database
$sql = "SELECT product_id, description, price, stock, category_id, product_image FROM product ORDER BY product_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <!-- Link the external CSS file -->
    <link rel="stylesheet" href="../../design/admin/manageproducts.css">
</head>
<body>

<?php include('../../style/adminheader.php'); ?>

<div class="container">
    <h2>MANAGE PRODUCTS</h2>

    <!-- Add Product Button -->
    <button class="addBtn" href="../../admin/products/addnewproduct.php">ADD PRODUCT</button>

    <!-- Display all products in a table -->
    <table class="table">
        <thead>
            <tr>
                <th>PRODUCT ID</th>
                <th>DESCRIPTION</th>
                <th>PRICE</th>
                <th>STOCKS</th>
                <th>CATEGORY ID</th>
                <th>PRODUCT IMAGE</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['product_id']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo 'PHP ' . number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td><?php echo $row['category_id']; ?></td>
                        <td><img src="../../media/products/<?php echo $row['product_image']; ?>" alt="Product Image"></td>
                        <td>
                            <!-- Edit and Delete buttons -->
                            <button class="editBtn" onclick="openEditForm(<?php echo $row['product_id']; ?>)">Edit</button>
                            <button class="deleteBtn" onclick="openDeleteForm(<?php echo $row['product_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="7">No products found.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Floating Add Product Form -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddProductForm()">&times;</span>
        <h2>ADD PRODUCT</h2>
        <form action="../../admin/products/addproduct.php" method="POST" enctype="multipart/form-data">
            <label for="productImage">Product Image</label>
            <input type="file" name="product_image" id="productImage" required>

            <label for="description">DESCRIPTION</label>
            <input type="text" name="description" id="description" required>

            <label for="price">PRICE</label>
            <input type="number" name="price" id="price" required>

            <label for="stock">STOCKS</label>
            <input type="number" name="stock" id="stock" required>

            <label for="category">CATEGORY ID</label>
            <input type="number" name="category_id" id="category" required>

            <button type="submit">ADD</button>
            <button type="button" onclick="closeAddProductForm()">Cancel</button>
        </form>
    </div>
</div>

<!-- The Edit Product Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>Edit Product</h2>
        <form id="editForm" action="editproduct.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="editProductId">
            <label for="editDescription">Description</label>
            <input type="text" name="description" id="editDescription" required>

            <label for="editStock">Stock</label>
            <input type="number" name="stock" id="editStock" required>

            <label for="editPrice">Price</label>
            <input type="number" name="price" id="editPrice" required>

            <label for="editCategory">Category ID</label>
            <input type="number" name="category_id" id="editCategory" required>

            <label for="editProductImage">Product Image</label>
            <input type="file" name="product_image" id="editProductImage">

            <button type="submit">Update Product</button>
        </form>
    </div>
</div>

<!-- The Delete Product Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        <h2>Are you sure you want to delete this product?</h2>
        <form id="deleteForm" action="deleteproduct.php" method="POST">
            <input type="hidden" name="product_id" id="deleteProductId">
            <button type="submit">Yes, Delete</button>
            <button type="button" onclick="closeModal('deleteModal')">Cancel</button>
        </form>
    </div>
</div>

<script>
    // Open the floating add product form
    function openAddProductForm() {
        document.getElementById('addProductModal').style.display = 'block';
    }

    // Close the floating add product form
    function closeAddProductForm() {
        document.getElementById('addProductModal').style.display = 'none';
    }

    // Open the edit form and populate it with existing product data
    function openEditForm(productId) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('editProductId').value = productId;

        // Fetch the product details to fill the form
        fetch(`get_product_details.php?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editDescription').value = data.description;
                document.getElementById('editStock').value = data.stock;
                document.getElementById('editPrice').value = data.price;
                document.getElementById('editCategory').value = data.category_id;
            });
    }

    // Open the delete form and populate it with the product_id
    function openDeleteForm(productId) {
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('deleteProductId').value = productId;
    }

    // Close modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
</script>
</body>
</html>
