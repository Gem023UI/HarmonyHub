<?php
session_start();
include("../style/config.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $city = trim($_POST['city']);

    // Regex validation
    $errors = [];

    // Validate firstname (only letters and spaces, max 50 characters)
    if (!preg_match("/^[a-zA-Z\s]{1,50}$/", $firstname)) {
        $errors[] = "Invalid firstname. Only letters and spaces are allowed (max 50 characters).";
    }

    // Validate lastname (only letters and spaces, max 50 characters)
    if (!preg_match("/^[a-zA-Z\s]{1,50}$/", $lastname)) {
        $errors[] = "Invalid lastname. Only letters and spaces are allowed (max 50 characters).";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate city (only letters and spaces, max 50 characters)
    if (!preg_match("/^[a-zA-Z\s]{1,50}$/", $city)) {
        $errors[] = "Invalid city. Only letters and spaces are allowed (max 50 characters).";
    }

    // If there are validation errors, store them in the session and redirect
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: profileinterface.php");
        exit();
    }

    // Handle profile picture upload
    $profile_picture = null;
    if (isset($_FILES['profilepicture']) && $_FILES['profilepicture']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../../media/profiles/";
        $file_name = basename($_FILES['profilepicture']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['profilepicture']['tmp_name'], $target_file)) {
            $profile_picture = $file_name;
        }
    }

    // Update the database
    $sql = "UPDATE customer SET firstname = ?, lastname = ?, email = ?, city = ?" . 
           ($profile_picture ? ", profilepicture = ?" : "") . " WHERE customer_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        if ($profile_picture) {
            $stmt->bind_param("sssssi", $firstname, $lastname, $email, $city, $profile_picture, $customer_id);
        } else {
            $stmt->bind_param("ssssi", $firstname, $lastname, $email, $city, $customer_id);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating profile: " . $conn->error;
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: " . $conn->error;
    }

    header("Location: profileinterface.php");
    exit();
}
?>
