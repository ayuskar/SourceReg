<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Already logged in, redirect to profile
    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureReg - Forgot Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

</head>
<body>
    <!-- Header Section -->
    <?php include 'header.php';?>

    <!-- Main Content Section -->
    <main>
        <section class="hero-section">
            <div class="hero-content">
                <h1>Forgot Password</h1>
                <p>Enter your registered email to reset your password</p>
            </div>
            <div style="background-color: rgb(48, 47, 47);" class="register-form-container">
                <form id="forgot-password-form" method="post" action="updatepassword.php">
                    <?php if (isset($_GET['error'])) {?>
                    <div style="color: red; margin-bottom: 1em;">
                        <?= $_GET['error']?>
                    </div>
                    <?php }?>
                    <div class="form-group">
                        <label for="email">Registered Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter registered email" required>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Reset Password">
                    </div>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <p>&copy; 2025 SecureReg. All Rights Reserved.</p>
            <ul class="social-links">
                <!-- Social media links -->
            </ul>
        </div>
    </footer>
    <script>
function togglePassword(inputId, iconElement) {
  const passwordField = document.getElementById(inputId);

  if (passwordField.type === "password") {
    passwordField.type = "text";
    iconElement.textContent = "🙈";
  } else {
    passwordField.type = "password";
    iconElement.textContent = "👁️";
  }
}
</script>

</body>
</html>
