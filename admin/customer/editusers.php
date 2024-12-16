<?php
include('../../style/config.php');
session_start();

// Check if the user is logged in and has role_id = 1 (admin)
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: ../../user/login.php"); // Redirect unauthorized users to the login page
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $city = trim($_POST['city']);
    $role_id = $_POST['role_id'];
    $status_id = $_POST['status_id'];
    $profilepicture = $_FILES['profilepicture']['name'];

    // Validation using regex
    $errors = [];

    // Validate firstname (letters and spaces, max 50 characters)
    if (!preg_match("/^[a-zA-Z\s]{1,50}$/", $firstname)) {
        $errors[] = "Invalid firstname. Only letters and spaces are allowed (max 50 characters).";
    }

    // Validate lastname (letters and spaces, max 50 characters)
    if (!preg_match("/^[a-zA-Z\s]{1,50}$/", $lastname)) {
        $errors[] = "Invalid lastname. Only letters and spaces are allowed (max 50 characters).";
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate city (letters and spaces, max 50 characters)
    if (!preg_match("/^[a-zA-Z\s]{1,50}$/", $city)) {
        $errors[] = "Invalid city. Only letters and spaces are allowed (max 50 characters).";
    }

    // Validate role_id (numeric)
    if (!preg_match("/^\d+$/", $role_id)) {
        $errors[] = "Invalid role ID. It must be a number.";
    }

    // Validate status_id (numeric)
    if (!preg_match("/^\d+$/", $status_id)) {
        $errors[] = "Invalid status ID. It must be a number.";
    }

    // If there are validation errors, store them in the session and redirect
    if (!empty($errors)) {
        $_SESSION['error'] = implode("<br>", $errors);
        header("Location: manageusers.php");
        exit();
    }

    // Handle profile picture upload
    if (!empty($profilepicture)) {
        $target_dir = "../../media/profiles/";
        $target_file = $target_dir . basename($profilepicture);

        if (!move_uploaded_file($_FILES['profilepicture']['tmp_name'], $target_file)) {
            $_SESSION['error'] = 'Error uploading profile picture.';
            header("Location: manageusers.php");
            exit();
        }
    } else {
        // If no new profile picture is uploaded, retain the old one
        $stmt = $conn->prepare("SELECT profilepicture FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $stmt->bind_result($profilepicture);
        $stmt->fetch();
        $stmt->close();
    }

    // Update the customer details in the database
    $sql = "UPDATE customer SET firstname = ?, lastname = ?, email = ?, city = ?, role_id = ?, status_id = ?, profilepicture = ? WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssissi", $firstname, $lastname, $email, $city, $role_id, $status_id, $profilepicture, $customer_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Customer updated successfully!';
    } else {
        $_SESSION['error'] = 'Error updating customer.';
    }

    $stmt->close();
    $conn->close();

    header("Location: manageusers.php");
    exit();
}
?>
