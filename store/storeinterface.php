<?php
session_start();
include("../user/config.php");

// Check if the user is logged in and fetch their role_id
if (!isset($_SESSION['role_id'])) {
    header("Location: ../user/login.php");
    exit();
}

// Include necessary files
include('../style/storeheader.php');
include('../user/config.php');

// Debugging output (remove in production)
//print_r($_SESSION);

// Check if the user is logged in
if (!isset($_SESSION['customer_id']) || !isset($_SESSION['role_id'])) {
    $_SESSION['error'] = 'Please log in first';
    header("Location: login.php");
    exit();
}

// Get the first name of the current customer
$firstname = $_SESSION['firstname'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Interface</title>
    <link rel="stylesheet" href="../design/store/storeinterface.css">
    <link href="https://fonts.googleapis.com/css2?family=Host+Grotesk:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <!-- Home Section -->
        <section id="home">
            <div>
            <h1>Welcome, <?= htmlspecialchars($firstname); ?></h1>
            <p>Explore a curated section of versatile guitars suitable for young and professional musicians alike.</p>
            </div>        
        </section>
        <!-- Acoustic Section -->
        <section id="acoustic">
            <a href="../store/categories/acoustic.php" class="acoustic-link">
                <div class="acoustic-content">
                    <div class="acoustic-image-container">
                        <img src="../media/categories/acoustic.png" alt="Acoustic Guitar" class="acoustic-image">
                    </div>
                    <div class="acoustic-text">
                        <h2>ACOUSTIC</h2>
                        <p>An acoustic guitar is known for its natural, resonant sound and versatility across musical styles. Its body shape, tonewoods like spruce or mahogany, and bracing design influence its tone, ranging from bright and clear to warm and mellow. With steel or nylon strings, it adapts to genres like folk, pop, or classical, making it a timeless instrument.</p>
                    </div>
                </div>
            </a>
        </section>
        <!-- Les Paul Section -->
        <section id="lespaul">
            <a href="../store/categories/lespaul.php" class="lespaul-link">
                <div class="lespaul-content">
                    <div class="lespaul-text">
                        <h2>LES PAUL</h2>
                        <p>The Les Paul is a legendary electric guitar known for its solid body, warm tone, and sustained resonance. Designed by Gibson, it features dual humbucking pickups that reduce noise and deliver a rich, full sound ideal for rock, blues, and jazz. Its iconic single-cutaway design, mahogany body, maple top, and sleek neck provide both visual appeal and comfortable playability, making it a favorite among guitarists worldwide.</p>
                    </div>
                    <div class="lespaul-image-container">
                        <img src="../media/categories/les paul.png" alt="Les Paul Guitar" class="lespaul-image">
                    </div>
                </div>
            </a>
        </section>
        <!-- Stratocaster Section -->
        <section id="stratocaster">
            <a href="../store/categories/stratocaster.php" class="acoustic-link">
                <div class="stratocaster-content">
                    <div class="stratocaster-image-container">
                        <img src="../media/categories/stratocaster.png" alt="Stratocaster Guitar" class="stratocaster-image">
                    </div>
                    <div class="stratocaster-text">
                        <h2>STRATOCASTER</h2>
                        <p>The Stratocaster, designed by Fender, is an iconic electric guitar known for its versatile tone and ergonomic design. Its double-cutaway body allows easy access to higher frets, while the three single-coil pickups and a 5-way selector switch offer a wide range of tones, from bright and crisp to warm and mellow. The tremolo bridge adds expressive pitch modulation, making it popular in genres like rock, blues, and pop. Renowned for its sleek contours and timeless style, the Stratocaster is a favorite among guitarists of all levels.</p>
                    </div>
                </div>
            </a>
        </section>
        <!-- Telecaster Section -->
        <section id="telecaster">
            <a href="../store/categories/telecaster.php" class="telecaster-link">
                <div class="telecaster-content">
                    <div class="telecaster-text">
                        <h2>TELECASTER</h2>
                        <p>The Telecaster, also designed by Fender, is a classic electric guitar known for its bright, cutting tone and simple, yet robust design. Featuring a solid single-cutaway body and two single-coil pickups, it delivers a sharp, punchy sound with a distinctive twang that has made it a staple in country, rock, and blues music. The Telecaster’s straightforward controls and durable build make it a versatile, reliable instrument, favored by many iconic guitarists for its clean tone and exceptional sustain.</p>
                    </div>
                    <div class="telecaster-image-container">
                        <img src="../media/categories/telecaster.png" alt="Telecaster Guitar" class="telecaster-image">
                    </div>
                </div>
            </a>
        </section>
        <!--Accessories Section -->
        <section id="accessories">
            <a href="../store/categories/accessories.php" class="accessories-link">
                <div class="accessories-content">
                    <div class="accessories-image-container">
                        <img src="../media/categories/accessories.png" alt="Accessories Guitar" class="accessories-image">
                    </div>
                    <div class="accessories-text">
                        <h2>ACCESSORIES</h2>
                        <p>Guitar accessories like picks, straps, capos, and strings enhance playability and sound. Picks offer different tones, straps provide comfort, capos help with key changes, and strings come in various materials for distinct tonal qualities.</p>
                    </div>
                </div>
            </a>
        </section>
        <!-- Customizable Section -->
        <section id="custom">
            <h2>Customizable Section</h2>
            <p>This is a customizable section where you can add your own content, such as advertisements, promotions, or other relevant information.</p>
        </section>
    </div>
</body>

</html>
