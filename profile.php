<?php
require_once 'db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "secure_reg";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

// Retrieve logged-in user's information from database
$stmt = $conn->prepare("SELECT full_name, username, email FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($full_name, $username, $email);
$stmt->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $updated_full_name = $_POST["full_name"];
    $updated_username = $_POST["username"];
    $updated_email = $_POST["email"];

    // Check if new username is already taken
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND username != ?");
    $stmt->bind_param("ss", $updated_username, $_SESSION['username']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Username already exists. Please choose another one.";
    } else {
        // Check if new email is already taken by another user
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND username != ?");
        $stmt->bind_param("ss", $updated_email, $_SESSION['username']);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Email already exists. Please use a different one.";
        } else {
            // Update user information in database
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ? WHERE username = ?");
            $stmt->bind_param("ssss", $updated_full_name, $updated_username, $updated_email, $_SESSION['username']);

            if ($stmt->execute()) {
                // Update session and local variables
                $_SESSION['username'] = $updated_username;
                $username = $updated_username;
                $full_name = $updated_full_name;
                $email = $updated_email;
                $success_message = "Update Successful!";
            } else {
                $error_message = "Error updating information: " . $stmt->error;
            }
        }
    }

    $stmt->close();
}


$rateLimitCheck = checkRateLimit('profile');

if (is_array($rateLimitCheck) && $rateLimitCheck['error']) {
    // Display rate limit error message
    $rateLimitError = $rateLimitCheck['message'];
} else {
    // No rate limit exceeded, proceed with login
    $rateLimitError = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureReg - Profile</title>
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
            <div class="hero-content">
                <h1>Profile</h1>
                <p>Welcome, <strong id="username-display"></strong><?= $full_name?>!</p>
                <div id="profile-info"></div>
                <?php if (isset($_GET['success']) && $_GET['success'] === 'true') :?>
        <div id="password-update-success-msg" style="color: green; margin-bottom: 1em;">
            <?= $_GET['message']?>
            <script>
                // Hide the password update success message after 10 seconds
                setTimeout(() => {
                    document.getElementById('password-update-success-msg').style.display = 'none';
                }, 10000); // 10000 milliseconds = 10 seconds
            </script>
        </div>
    <?php endif;?>
                 <?php if ($rateLimitError):?>
    <div class="alert alert-danger" role="alert">
        <?= $rateLimitError?>
    </div>
<?php endif;?>
 <?php if (isset($success_message)) :?>
                <div style="color: green; margin-bottom: 1em;">
                    <?= $success_message?>
                </div>
            <?php endif;?>
            <?php if (isset($error_message)) :?>
                <div style="color: red; margin-bottom: 1em;">
                    <?= $error_message?>
                </div>
            <?php endif;?>
    <div id="profile-info">
        <form method="post">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" value="<?= $full_name?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?= $username?>" required>
                <small id="username-help">Choose a unique username.</small>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= $email?>" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Update">
            </div>
            <div class="update-password-section">
        <div class="forgot-password" >
    <a style="margin-bottom:10px" href="updateprofilepassword.php">Update Password?</a>
</div>
    </div>
           
        </form>
    </div>
</div>

            </div>
        </section>
         
    </div>
    </main>

    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <p>&copy; 2025 SecureReg. All Rights Reserved.</p>
            <ul class="social-links">
                <!-- Social links -->
            </ul>
        </div>
    </footer>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
