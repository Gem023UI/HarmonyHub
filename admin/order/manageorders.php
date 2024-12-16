<?php
include("../../style/adminheader.php");
include("../../style/config.php"); // Include your database configuration

session_start(); // Ensure session is started

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Redirect unauthorized users to an error or login page
    header("Location: ../../user/login.php"); // Redirect to login page
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update the status of the order
    if (isset($_POST['orderinfo_id'], $_POST['status'])) {
        $orderinfo_id = (int)$_POST['orderinfo_id'];
        $status_id = (int)$_POST['status'];

        $sql = "UPDATE orderinfo SET status_id = ? WHERE orderinfo_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $status_id, $orderinfo_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "<p>Order ID {$orderinfo_id} status updated successfully.</p>";
            } else {
                echo "<p>No changes were made to Order ID {$orderinfo_id}.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Error updating the order: " . $conn->error . "</p>";
        }
    }
}

// Fetch current orders
$sql = "
    SELECT 
        oi.orderinfo_id,
        c.customer_id,
        c.firstname,
        c.lastname,
        c.email,
        c.city,
        p.description AS product_description,
        ol.quantity,
        p.price,
        oi.date_placed,
        os.description AS status
    FROM 
        orderinfo oi
    JOIN 
        customer c ON oi.customer_id = c.customer_id
    JOIN 
        orderline ol ON oi.orderinfo_id = ol.orderinfo_id
    JOIN 
        product p ON ol.prod_id = p.product_id
    JOIN 
        order_status os ON oi.status_id = os.os_id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link rel="stylesheet" href="../../design/admin/manageorders.css"> <!-- Link to your CSS file -->
</head>

<body>
    <h1>Current Orders</h1>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>City</th>
                <th>Product Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Date Placed</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['orderinfo_id']; ?></td>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td><?php echo $row['firstname']; ?></td>
                        <td><?php echo $row['lastname']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['city']; ?></td>
                        <td><?php echo $row['product_description']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['date_placed']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="orderinfo_id" value="<?php echo $row['orderinfo_id']; ?>">
                                <select name="status">
                                    <?php
                                    $statusQuery = "SELECT os_id, description FROM order_status";
                                    $statusResult = $conn->query($statusQuery);

                                    if ($statusResult && $statusResult->num_rows > 0) {
                                        while ($statusRow = $statusResult->fetch_assoc()) {
                                            $selected = $row['status'] === $statusRow['description'] ? 'selected' : '';
                                            echo "<option value='{$statusRow['os_id']}' {$selected}>{$statusRow['description']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="12">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>
