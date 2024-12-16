<?php
session_start();
include('../../style/config.php');

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    // Redirect unauthorized users to an error page or login page
    header("Location: ../../user/login.php"); // Replace 'error.php' with the actual error page
    exit();
}

// Fetch all customers from the database
$sql = "SELECT customer_id, firstname, lastname, email, city, role_id, status_id, profilepicture FROM customer ORDER BY customer_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customer</title>
    <!-- Link the external CSS file -->
    <link rel="stylesheet" href="../../design/admin/manageusers.css">
</head>
<body>

<?php include('../../style/adminheader.php'); ?>

<div class="container">
    <h2>MANAGE USERS</h2>

    <!-- Display all customers in a table -->
    <table class="table">
        <thead>
            <tr>
                <th>CUSTOMER ID</th>
                <th>PROFILE PICTURE</th>
                <th>FIRST NAME</th>
                <th>LAST NAME</th>
                <th>EMAIL</th>
                <th>CITY</th>
                <th>ROLE ID</th>
                <th>STATUS ID</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0) { ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['customer_id']; ?></td>
                        <td>
                            <img src="../../media/profiles/<?php echo $row['profilepicture']; ?>" alt="Profile Picture" style="width:50px; height:50px; border-radius:10%;">
                        </td>
                        <td><?php echo $row['firstname']; ?></td>
                        <td><?php echo $row['lastname']; ?></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['city']; ?></td>
                        <td><?php echo $row['role_id']; ?></td>
                        <td><?php echo $row['status_id']; ?></td>
                        <td>
                            <!-- Edit and Delete buttons -->
                            <button class="editBtn" onclick="openEditForm(<?php echo $row['customer_id']; ?>)">Edit</button>
                            <button class="deleteBtn" onclick="openDeleteForm(<?php echo $row['customer_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="9">No customers found.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- The Edit Customer Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')">&times;</span>
        <h2>Edit Customer</h2>
        <form id="editForm" action="editusers.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="customer_id" id="editCustomerId">

            <label for="editFirstName">First Name</label>
            <input type="text" name="firstname" id="editFirstName" required>

            <label for="editLastName">Last Name</label>
            <input type="text" name="lastname" id="editLastName" required>

            <label for="editEmail">Email</label>
            <input type="email" name="email" id="editEmail" required>

            <label for="editCity">City</label>
            <input type="text" name="city" id="editCity" required>

            <label for="editRole">Role ID</label>
            <input type="number" name="role_id" id="editRole" required>

            <label for="editStatus">Status ID</label>
            <input type="number" name="status_id" id="editStatus" required>

            <label for="editProfilePicture">Profile Picture</label>
            <input type="file" name="profilepicture" id="editProfilePicture">

            <button type="submit">Update Customer</button>
        </form>
    </div>
</div>

<!-- The Delete Customer Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        <h2>Are you sure you want to delete this customer?</h2>
        <form id="deleteForm" action="deleteusers.php" method="POST">
            <input type="hidden" name="customer_id" id="deleteCustomerId">
            <button type="submit">Yes, Delete</button>
            <button type="button" onclick="closeModal('deleteModal')">Cancel</button>
        </form>
    </div>
</div>

<script>
    // Open the edit form and populate it with existing customer data
    function openEditForm(customerId) {
        document.getElementById('editModal').style.display = 'block';
        document.getElementById('editCustomerId').value = customerId;

        // Fetch the customer details to fill the form
        fetch(`get_customer_details.php?customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editFirstName').value = data.firstname;
                document.getElementById('editLastName').value = data.lastname;
                document.getElementById('editEmail').value = data.email;
                document.getElementById('editCity').value = data.city;
                document.getElementById('editRole').value = data.role_id;
                document.getElementById('editStatus').value = data.status_id;
            });
    }

    // Open the delete form and populate it with the customer ID
    function openDeleteForm(customerId) {
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('deleteCustomerId').value = customerId;
    }

    // Close modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }
</script>
</body>
</html>
