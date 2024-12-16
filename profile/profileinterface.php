<?php
session_start();
include("../style/profileheader.php");
include("../style/config.php");

// Check if user is logged in and has a valid customer_id
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['error'] = 'Please log in to view your profile.';
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Fetch user data based on customer_id
$sql = "SELECT firstname, lastname, email, city, profilepicture FROM customer WHERE customer_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = 'No user found.';
        header("Location: login.php");
        exit();
    }
    $stmt->close();
} else {
    $_SESSION['error'] = 'Database query failed.';
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>
    <link rel="stylesheet" href="../design/profile/profileinterface.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h3>Account Details</h3>
            <form action="profilecustomize.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <!-- First Name and Last Name -->
                <div class="form-row">
                    <div class="form-group half-width">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                    </div>
                    <div class="form-group half-width">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                    </div>
                </div>

                <!-- City -->
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                </div>

                <!-- Profile Picture -->
                <div class="form-group">
                    <label for="profilepicture">Profile Picture</label>
                    <input type="file" id="profilepicture" name="profilepicture" accept="image/*">
                    <img 
                        src="<?php echo !empty($user['profilepicture']) ? '../../media/profiles/' . htmlspecialchars($user['profilepicture']) : '../../media/profiles/default.png'; ?>" 
                        alt="Profile Picture" 
                        style="max-width: 150px;">
                </div>

                <button type="submit" class="update-btn">Update</button>
            </form>
        </div>
    </div>
</body>
</html>
