<?php
session_start();
include('../user/config.php');

// Check if the user is logged in
if (!isset($_SESSION['customer_id'])) {
    $_SESSION['error'] = 'Please log in to leave a review.';
    header("Location: login.php");
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_text'], $_POST['rating'], $_POST['prod_id'])) {
    $customer_id = $_SESSION['customer_id'];
    $product_id = intval($_POST['prod_id']);
    $review_text = trim($_POST['review_text']);
    $rating = intval($_POST['rating']);
    
    // Input validation
    if ($rating < 1 || $rating > 5 || empty($review_text)) {
        $_SESSION['error'] = 'Invalid input. Please provide a rating between 1 and 5 and a valid review text.';
        header("Location: submit_review.php?prod_id=$product_id");
        exit();
    }
    
    // Sanitize the review text
    function sanitizeReviewText($text) {
        $forbidden_words = ['gago', 'tanga', 'tanginamo', 'hayop', 'tarantado', 'puta', 'potangina', 'putangina', 'kupal', 'fuck', 'tangina', 'shit'];
        foreach ($forbidden_words as $word) {
            $text = preg_replace("/\b" . preg_quote($word, '/') . "\b/i", '****', $text);
        }
        return $text;
    }
    $review_text = sanitizeReviewText($review_text);
    
    // Insert the review into the database
    $query = "INSERT INTO reviews (product_id, customer_id, review_text, rating, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisi", $product_id, $customer_id, $review_text, $rating);

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Review submitted successfully!';
        header("Location: viewreviews.php?prod_id=$product_id");
    } else {
        $_SESSION['error'] = 'Failed to submit review. Please try again later.';
        header("Location: submit_review.php?prod_id=$product_id");
    }
} else {
    $_SESSION['error'] = 'Invalid request.';
    header("Location: viewreviews.php");
}

$conn->close();
?>
