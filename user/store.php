
<?php
session_start();
include("../style/config.php");
include("../style/header.php");

$email = trim($_POST['email']);
$password = trim($_POST['password']);
$confirmPass = trim($_POST['confirmPass']);
if ($password !== $confirmPass) {
    $_SESSION['message'] = 'passwords do not match';
    header("Location: register.php");
    exit();
}
$password = sha1($password);
$sql = "INSERT INTO customer (email, password) VALUES('$email', '$password')";

$result = mysqli_query($conn, $sql);
if ($result ) {
    $_SESSION['customer_id'] = mysqli_insert_id($conn);
    header("Location: profile.php");
}
