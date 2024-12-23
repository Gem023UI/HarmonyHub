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
  <link rel="stylesheet" href="../../design/header/adminheader.css"/>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <title>ADMIN ACCESS</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg py-2">
    <div class="container-fluid d-flex align-items-center">
      <!-- Logo and Store Name on the left -->
      <div class="d-flex align-items-center me-auto">
        <img src="../media/harmonyhub_logo.png" class="logo" alt="Logo">
        <a class="navbar-brand" href="../../store/storeinterface.php">Harmony Hub</a>
      </div>

      <!-- Centered ADMIN ACCESS text -->
      <div class="mx-auto text-center admin-access">
        <span class="text-uppercase fw-bold">ADMIN ACCESS</span>
      </div>

      <!-- Right-aligned icons -->
      <div class="navbar-icons ms-auto">
        <div class="cart-icon">
          <a href="../../cart/cartinterface.php" class="nav-icon"><i class="fa-solid fa-cart-shopping"></i></a>
        </div>
        <div class="order-icon">
          <a href="../../orders/orderinterface.php" class="nav-icon"><i class="fa-solid fa-business-time"></i></a>
        </div>
        <!-- Assuming you have a session or a variable that stores the logged-in user's customer_id -->
        <div class="user-icon">
          <a href="../../profile/profileinterface.php?customer_id=<?php echo $_SESSION['customer_id']; ?>" class="nav-icon">
          <i class="fa-solid fa-user"></i>
          </a>
        </div>


        <!-- Settings Dropdown -->
        <div class="dropdown">
          <a href="#" class="nav-icon dropdown-toggle" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fa-solid fa-cog"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
            <li><a class="dropdown-item" href="../../admin/products/manageproducts.php">PRODUCTS</a></li>
            <li><a class="dropdown-item" href="../../admin/customer/manageusers.php">CUSTOMERS</a></li>
            <li><a class="dropdown-item" href="../../admin/order/manageorders.php">ORDERS</a></li>
            <li><a class="dropdown-item" href="../user/logout.php">LOGOUT</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>
</body>
</html>
