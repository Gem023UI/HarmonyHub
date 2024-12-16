<?php
session_start();
include("../style/loginheader.php");
include("../user/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT customer_id, email, password, firstname, role_id, status_id FROM customer WHERE email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = 'Error preparing SQL statement: ' . $conn->error;
        header("Location: login.php");
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Check if the user's role_id is 2
        if ($user['status_id'] === 2) {
            $_SESSION['error'] = "Your account has been deactivated by the admin. Use or create another account.";
            $stmt->close();
            header("Location: login.php");
            exit();
        }

        if (password_verify($password, $user['password'])) {
            $_SESSION['customer_id'] = $user['customer_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['status_id'] = $user['status_id'];

            $_SESSION['success'] = 'Login successful! Welcome ' . $user['firstname'];
            header("Location: ../store/storeinterface.php");
            exit();
        } else {
            $_SESSION['error'] = 'Invalid password.';
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
    }

    $stmt->close();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN PAGE</title>
    <!-- Link to the CSS Directory -->
    <link rel="stylesheet" href="../design/user/login.css">
</head>

<body>
    <div class="background"></div>
    <div class="login-container">
        <div class="login-header">
            <h2>LOGIN</h2>
        </div>
        <div class="login-body">
            <?php include("../user/alert.php"); ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label for="email">EMAIL</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">PASSWORD</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="login-btn">LOGIN</button>
                <div class="register-link">
                    <a href="../user/register.php">Don't have an account? REGISTER.</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>

