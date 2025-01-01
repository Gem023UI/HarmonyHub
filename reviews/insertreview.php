<?php
session_start();
include('../user/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['error'] = 'Please log in to leave a review.';
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $product_id = isset($_POST['prod_id']) ? intval($_POST['prod_id']) : 0;
    $orderinfo_id = isset($_POST['orderinfo_id']) ? intval($_POST['orderinfo_id']) : 0;
    $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

    // Debugging: Log or echo the input values (remove this in production)
    // echo "Product ID: $product_id, Order Info ID: $orderinfo_id, Rating: $rating, Review Text: $review_text";

    // Check if the product ID and order info ID are valid
    if ($product_id <= 0 || $orderinfo_id <= 0) {
        $_SESSION['error'] = 'Invalid product ID or order info ID.';
        header("Location: submit_review.php");
        exit();
    }

    // Check for valid rating and non-empty review text
    if ($rating < 1 || $rating > 5 || empty($review_text)) {
        $_SESSION['error'] = 'Invalid input. Please provide a rating between 1 and 5 and a valid review text.';
        header("Location: submit_review.php?prod_id=$product_id&orderinfo_id=$orderinfo_id");
        exit();
    }

    // Sanitize review text
    function sanitizeReviewText($text) {
        $forbidden_words = ['gago', 'tanga', 'tanginamo', 'hayop', 'tarantado', 'puta', 'potangina', 'putangina', 'kupal', 'fuck', 'tangina', 'shit'];
        foreach ($forbidden_words as $word) {
            $text = preg_replace("/\b" . preg_quote($word, '/') . "\b/i", '****', $text);
        }
        return $text;
    }
    $review_text = sanitizeReviewText($review_text);

    // Insert the review into the database
    $query = "INSERT INTO reviews (product_id, customer_id, orderinfo_id, review_text, rating, created_at) 
              VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiisi", $product_id, $_SESSION['customer_id'], $orderinfo_id, $review_text, $rating);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Review submitted successfully!';
        header("Location: viewreviews.php?prod_id=$product_id&orderinfo_id=$orderinfo_id");
    } else {
        $_SESSION['error'] = 'Failed to submit review. Please try again later.';
        header("Location: viewreviews.php?prod_id=$product_id&orderinfo_id=$orderinfo_id");
    }
} else {
    $_SESSION['error'] = 'Invalid request.';
    header("Location: viewreviews.php");
}

$conn->close();
?>
