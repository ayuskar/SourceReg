<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureReg</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header Section -->
        <?php include 'header.php'; ?>


    <!-- Main Content Section -->
    <main>
        

        <section class="hero-section">
            <div class="alert alert-success" id="registration-success-alert">
    <strong>Registration Successful!</strong> 
    Welcome to SecureReg, <strong id="username-display"></strong>!
    <button class="btn btn-success btn-ok" onclick="redirectToIndex()">X</button>
    <!-- You can add more HTML elements here to match your desired design -->
    <!-- e.g., <i class="fas fa-check-circle"></i> for a check circle icon -->
</div>

            <div class="hero-content">
                <h1>Secure Your Digital Presence</h1>
                <p>Register now and experience the future of online security</p>
            </div>
             <section class="hero-section">
    <div class="alert alert-success" id="registration-success-alert">
        <!-- Your registration success alert content -->
    </div>

    <?php if (isset($_GET['logged_out']) && $_GET['logged_out'] == 'true'):?>
        <div class="alert alert-success" id="logout-message" style="color: green;">
            You have been successfully logged out.
        </div>
        <script>
            setTimeout(function() {
                document.getElementById("logout-message").style.display = "none";
            }, 10000); // 10000 milliseconds = 10 seconds
        </script>
    <?php endif;?>
            <?php if (isset($_SESSION['logout_message'])): ?>
    <div class="alert alert-info">
        <?= $_SESSION['logout_message']; ?>
    </div>
    <?php unset($_SESSION['logout_message']); ?>
<?php endif; ?>

            

    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <p>&copy; 2025 SecureReg. All Rights Reserved.</p>
            <ul class="social-links">
    <li>
        <a href="#" target="_blank">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
            </svg>
        </a>
    </li>
    <li>
        <a href="#" target="_blank">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/>
            </svg>
        </a>
    </li>
    <li>
        <a href="#" target="_blank">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5"/>
                <path d="M16 11.37A4 4 0 1 1 12.63 16 4 4 0 0 1 16 11.37z"/>
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
            </svg>
        </a>
    </li>
</ul>

        </div>
    </footer>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="script.js"></script>
   

</body>
</html>
