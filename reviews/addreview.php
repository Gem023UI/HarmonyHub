<?php
session_start();
include('../style/reviewheader.php');
include('../user/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['error'] = 'Please log in first';
    header("Location: login.php");
    exit();
}

// Check if product ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prod_id'])) {
    $product_id = intval($_POST['prod_id']);
} else {
    echo "<p>Invalid request.</p>";
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'], $_POST['rating'])) {
    $review_text = trim($_POST['review_text']);
    $rating = intval($_POST['rating']);
    $customer_id = $_SESSION['customer_id'];

    if ($rating >= 1 && $rating <= 5 && !empty($review_text)) {
        $insert_query = "INSERT INTO reviews (product_id, customer_id, review_text, rating, created_at)
                         VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("iisi", $product_id, $customer_id, $review_text, $rating);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Review submitted successfully!';
            header("Location: viewreviews.php?prod_id=$product_id");
            exit();
        } else {
            echo "<p>Failed to submit review. Please try again later.</p>";
        }
    } else {
        echo "<p>Please provide a valid rating (1-5) and review text.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="../design/store/reviewinterface.css">
</head>
<body>
    <div class="review-form-container">
        <h2>Submit Your Review</h2>
        <form action="insertreview.php" method="post">
            <input type="hidden" name="prod_id" value="<?php echo $product_id; ?>">
            <label for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required>
            <label for="review_text">Your Review:</label>
            <textarea id="review_text" name="review_text" rows="5" required></textarea>
            <button type="submit" class="submit-review-btn">Submit Review</button>
        </form>
    </div>
</body>
</html>
