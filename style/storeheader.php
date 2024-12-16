<?php
$role_id = $_SESSION['role_id']; // Retrieve role_id from the session
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  
  <!-- FONTAWESOME -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <!-- CSS DIRECTORY -->
  <link rel="stylesheet" href="../design/header/storeheader.css"/>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <title>CATEGORIES</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg py-2">
    <div class="container-fluid d-flex align-items-center">
      <!-- Logo and Store Name on the left -->
      <div class="d-flex align-items-center me-auto">
        <img src="../media/harmonyhub_logo.png" class="logo" alt="Logo">
        <a class="navbar-brand" href="/SYSTEM/store/storeinterface.php">Harmony Hub</a>

        <!-- Category Dropdown -->
        <div class="dropdown ms-3">
          <a class="navbar-brand dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Categories
          </a>
          <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
            <li><a class="dropdown-item" href="/SYSTEM/store/categories/acoustic.php">Acoustic</a></li>
            <li><a class="dropdown-item" href="/SYSTEM/store/categories/lespaul.php">Les Paul</a></li>
            <li><a class="dropdown-item" href="/SYSTEM/store/categories/stratocaster.php">Stratocaster</a></li>
            <li><a class="dropdown-item" href="/SYSTEM/store/categories/telecaster.php">Telecaster</a></li>
            <li><a class="dropdown-item" href="/SYSTEM/store/categories/accessories.php">Accessories</a></li>
          </ul>
        </div>
      </div>

      <!-- Search input and icon in the center -->
      <div class="d-flex align-items-center mx-auto">
        <form action="search.php" method="GET" class="d-flex align-items-center">
          <input class="form-control search-input" type="search" placeholder="Search" aria-label="Search" name="search">
          <button class="btn search-btn" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
          </button>
        </form>
      </div>

      <!-- Right-aligned icons -->
      <div class="navbar-icons ms-auto">
        <div class="cart-icon">
          <a href="../cart/cartinterface.php" class="nav-icon"><i class="fa-solid fa-cart-shopping"></i></a>
        </div>
        <div class="order-icon">
          <a href="../orders/order_history.php" class="nav-icon"><i class="fa-solid fa-business-time"></i></a>
        </div>
        <div class="user-icon">
          <a href="../profile/profileinterface.php" class="nav-icon"><i class="fa-solid fa-user"></i></a>
        </div>

        <!-- Settings Dropdown -->
        <div class="dropdown">
          <a href="#" class="nav-icon dropdown-toggle" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-cog"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown" id="dropdownMenu">
          <?php if ($role_id == 1): ?>
            <li><a class="dropdown-item" href="/harmonyhub/admin/products/manageproducts.php">PRODUCTS</a></li>
            <li><a class="dropdown-item" href="/harmonyhub/admin/customer/manageusers.php">CUSTOMERS</a></li>
            <li><a class="dropdown-item" href="/harmonyhub/admin/order/manageorders.php">ORDERS</a></li>
          <?php else: ?>
            <li><a class="dropdown-item customer-restricted" href="../user/logout.php">PRODUCTS</a></li>
            <li><a class="dropdown-item customer-restricted" href="../user/logout.php">CUSTOMERS</a></li>
            <li><a class="dropdown-item customer-restricted" href="../user/logout.php">ORDERS</a></li>
          <?php endif; ?>
            <li><a class="dropdown-item" href="../user/logout.php">LOGOUT</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- JavaScript -->
  <script>
    // Add confirmation alert before redirecting customers
    const roleId = <?php echo $role_id; ?>;
    if (roleId === 2) { // If customer
      const restrictedItems = document.querySelectorAll('.customer-restricted');
      restrictedItems.forEach(item => {
        item.addEventListener('click', (event) => {
          const confirmRedirect = confirm('Access denied: You are not authorized to access admin controls. You will be logged out.');
          if (!confirmRedirect) {
            event.preventDefault(); // Prevent redirection if they cancel
          }
        });
      });
    }
  </script>
</body>
</html>
