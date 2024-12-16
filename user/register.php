<?php
session_start();
include("../style/loginheader.php");
include("../style/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Account creation fields
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPass = trim($_POST['confirmPass']);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Profile fields
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $city = trim($_POST['city']);
    $profile_picture = null;

    // Validation: Check if passwords match
    if ($password !== $confirmPass) {
        $_SESSION['error'] = 'Passwords do not match.';
        header("Location: register.php");
        exit();
    }

    // Handle profile picture upload
    if (isset($_FILES['profilepicture'])) {
        $uploadError = $_FILES['profilepicture']['error'];

        // Check for upload errors
        if ($uploadError === UPLOAD_ERR_OK) {
            $target_dir = "../media/profiles/";

            // Ensure the target directory exists
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0777, true)) {
                    $_SESSION['error'] = 'Failed to create directory for profile pictures.';
                    header("Location: register.php");
                    exit();
                }
            }

            // Validate file type
            $file_ext = pathinfo($_FILES['profilepicture']['name'], PATHINFO_EXTENSION);
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($file_ext), $allowed_types)) {
                $_SESSION['error'] = 'Invalid file type. Allowed types: JPG, JPEG, PNG, GIF.';
                header("Location: register.php");
                exit();
            }

            // Create a unique file name
            $unique_name = uniqid() . "_" . time() . "." . $file_ext;
            $target_file = $target_dir . $unique_name;

            // Move the uploaded file
            if (!move_uploaded_file($_FILES['profilepicture']['tmp_name'], $target_file)) {
                $_SESSION['error'] = 'Failed to upload the profile picture. Please try again.';
                header("Location: register.php");
                exit();
            }

            $profile_picture = $target_file;
        } elseif ($uploadError !== UPLOAD_ERR_NO_FILE) {
            // Handle specific upload errors
            switch ($uploadError) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $_SESSION['error'] = 'The uploaded file exceeds the allowed size.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $_SESSION['error'] = 'The file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $_SESSION['error'] = 'Missing temporary folder on the server.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $_SESSION['error'] = 'Failed to write the file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $_SESSION['error'] = 'File upload stopped by a PHP extension.';
                    break;
                default:
                    $_SESSION['error'] = 'An unknown error occurred during file upload.';
            }
            header("Location: register.php");
            exit();
        }
    }

    // Insert user into the database
    $sql = "INSERT INTO customer (email, password, firstname, lastname, city, profilepicture) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $_SESSION['error'] = 'Error preparing SQL statement: ' . $conn->error;
        header("Location: register.php");
        exit();
    }

    $stmt->bind_param("ssssss", $email, $hashedPassword, $firstname, $lastname, $city, $profile_picture);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Registration successful! Please log in.';
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = 'Error registering user: ' . $stmt->error;
        header("Location: register.php");
        exit();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Link to CSS Styling -->
    <link rel="stylesheet" href="../design/user/register.css">
</head>

<body>
    <div class="background"></div>
    <div class="container">
        <!-- Image Upload Container -->
        <div class="image-container">
            <div class="container-label">
                <h3>Upload Profile Picture</h3>
            </div>
            <div class="image-preview" id="imagePreview">
                <img src="" alt="Image Preview" class="preview-image">
                <span class="preview-text">Image Preview</span>
            </div>
            <input type="file" id="profilepicture" name="profilepicture" accept="image/*" onchange="previewImage(event)">
        </div>

        <!-- Registration Form Container -->
        <div class="form-container">
            <div class="container-label">
                <h3>Create Account</h3>
            </div>
            <?php include("../style/alert.php"); ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <!-- Profile Picture -->
            <div class="form-group">
                <label for="profilepicture">Profile Picture</label>
                <input type="file" id="profilepicture" name="profilepicture" accept="image/*" required>
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Password and Confirm Password -->
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group half-width">
                    <label for="confirmPass">Confirm Password</label>
                    <input type="password" id="confirmPass" name="confirmPass" required>
                </div>
            </div>

            <!-- First Name and Last Name -->
            <div class="form-row">
                <div class="form-group half-width">
                    <label for="firstname">First Name</label>
                    <input type="text" id="firstname" name="firstname" required>
                </div>
                <div class="form-group half-width">
                    <label for="lastname">Last Name</label>
                    <input type="text" id="lastname" name="lastname" required>
                </div>
            </div>

            <!-- City -->
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>

            <!-- Register Button -->
            <div class="form-group">
                <button type="submit" class="register-btn">Register</button>
            </div>
        </form>
        </div>
    </div>

    <!-- JavaScript for Image Preview -->
    <script>
        function previewImage(event) {
            const imagePreview = document.getElementById('imagePreview');
            const previewImage = imagePreview.querySelector('.preview-image');
            const previewText = imagePreview.querySelector('.preview-text');

            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = () => {
                    previewImage.src = reader.result;
                    previewImage.style.display = 'block';
                    previewText.style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.src = '';
                previewImage.style.display = 'none';
                previewText.style.display = 'block';
            }
        }
    </script>
</body>

</html>
