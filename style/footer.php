<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Website Footer</title>
  
  <!-- Link to the CSS File -->
  <link rel="stylesheet" href="../design/header/footer.css">
</head>

<body>
  <footer class="footer">
    <p>Technological University of the Philippines - Taguig, Information Management System - Project    Malaga, Jemuel A. & Piad, Carl Evan C.    BSIT - S - 2A</p>
    <p id="currentDate"></p>
  </footer>

  <!-- Script to Fetch and Display Current Date -->
  <script>
    const currentDate = new Date();
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('currentDate').textContent = currentDate.toLocaleDateString(undefined, options);
  </script>
</body>

</html>
