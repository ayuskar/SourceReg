<?php
session_start();

require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST["email"])) {
        $email = $_POST["email"];
        $stmt = $conn->prepare("SELECT username FROM users WHERE email =?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($username);
            $stmt->fetch();
            // Generate a password reset token (e.g., a random string)
            $reset_token = bin2hex(random_bytes(16));
            // Store the token in the user's session or in the database (e.g., with a timestamp)
            $_SESSION['reset_token'] = $reset_token;
            $_SESSION['reset_username'] = $username;

            // Display the password update form
           ?>
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
    <style>
        .form-group {
  position: relative;
}

.toggle-password {
  position: absolute;
  right: 10px;
  top: 70%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #fff; /* Adjust as per background */
  font-size: 1.2em;
  user-select: none;
}

        .toggle-password {
    position: absolute;
    right: 10px;
    top: 70%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 1.2em;
}

.password-policy-tooltip {
    position: relative;
    display: inline-block;
    margin-left: 10px;
}

.tooltip-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    background-color: #007bff;
    color: white;
    border-radius: 50%;
    font-size: 12px;
    cursor: help;
    margin-bottom: 20px;
}

.tooltip-content {
    visibility: hidden;
    width: 250px;
    background-color: #333;
    color: #fff;
    text-align: left;
    border-radius: 6px;
    padding: 10px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
}

.password-policy-tooltip:hover .tooltip-content {
    visibility: visible;
    opacity: 1;
}
        </style>
</head>
<body>
    <!-- Header Section -->
    <?php include 'header.php';?>

    <!-- Main Content Section -->
    <main>
        <section class="hero-section">
            <div class="hero-content">
                <h1>Update Password</h1>
                <p>Enter your new password</p>
            </div>
            <div style="background-color: rgb(48, 47, 47);" class="register-form-container">
               
            <form id="update-password-form" method="post" action="updatepassword.php?token=<?= $reset_token?>">
   <div class="form-group">
    <label for="new_password">New Password:</label>
    <input type="password" id="new_password" name="new_password" required>
    <span class="toggle-password" onclick="togglePassword('new_password', this)">👁️</span>
</div>
    <!-- Password Strength Bar and Requirements -->
    <div style="margin-top: 10px;">
        <div id="strength-bar" style="height: 8px; background-color: #ccc; border-radius: 5px;">
            <div id="strength-fill" style="height: 8px; width: 0%; background-color: red; border-radius: 5px;"></div>
        </div>
        <div id="password-strength" style="margin-top: 5px; font-weight: bold;"></div>
        <ul id="password-requirements">
            <li id="length">Minimum 6 characters <span class="requirement-status" id="length-status"></span></li>
            <li id="uppercase">Uppercase & Lowercase characters <span class="requirement-status" id="uppercase-status"></span></li>
            <li id="number">At least one number <span class="requirement-status" id="number-status"></span></li>
            <li id="special">At least one special character <span class="requirement-status" id="special-status"></span></li>
        </ul>
    
</div>

    <div class="form-group">
        <label for="confirm_new_password">Confirm New Password:</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" required>
        <span class="toggle-password" onclick="togglePassword('confirm_new_password', this)">👁️</span>
    </div>
    <div class="password-policy-tooltip">
    <span class="tooltip-icon">i</span>
    <div style='margin-bottom:10px' class="tooltip-content">
                <h4>Password Requirements:</h4>
        <ul id="password-requirements">
            <li id="length">-Minimum 6 characters <span class="requirement-status"></span></li>
            <li id="uppercase">-Uppercase & Lowercase characters <span class="requirement-status"></span></li>
            <li id="number">-At least one number <span class="requirement-status"></span></li>
            <li id="special">-At least one special character <span class="requirement-status"></span></li>
        </ul>
    </div>
</div>
    <div class="form-group">
        <input type="submit" value="Update Password" id="update-password-submit">
    </div>
</form>

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

const passwordField = document.getElementById('new_password');
const confirmField = document.getElementById('confirm_new_password');
const submitButton = document.getElementById('update-password-submit');
const strengthFill = document.getElementById('strength-fill');
const strengthText = document.getElementById('password-strength');

passwordField.addEventListener('input', validatePassword);
confirmField.addEventListener('input', validatePassword);

function validatePassword() {
    const password = passwordField.value;
    const confirmPassword = confirmField.value;

    let strength = 0;

    // Length validation
    if (password.length >= 6) {
        document.getElementById('length-status').textContent = '✔️';
        document.getElementById('length-status').style.color = 'green';
        strength++;
    } else {
        document.getElementById('length-status').textContent = '❌';
        document.getElementById('length-status').style.color = 'red';
    }

    // Uppercase & Lowercase validation
    if (/[A-Z]/.test(password) && /[a-z]/.test(password)) {
        document.getElementById('uppercase-status').textContent = '✔️';
        document.getElementById('uppercase-status').style.color = 'green';
        strength++;
    } else {
        document.getElementById('uppercase-status').textContent = '❌';
        document.getElementById('uppercase-status').style.color = 'red';
    }

    // Number validation
    if (/\d/.test(password)) {
        document.getElementById('number-status').textContent = '✔️';
        document.getElementById('number-status').style.color = 'green';
        strength++;
    } else {
        document.getElementById('number-status').textContent = '❌';
        document.getElementById('number-status').style.color = 'red';
    }

    // Special character validation
    if (/[^A-Za-z0-9]/.test(password)) {
        document.getElementById('special-status').textContent = '✔️';
        document.getElementById('special-status').style.color = 'green';
        strength++;
    } else {
        document.getElementById('special-status').textContent = '❌';
        document.getElementById('special-status').style.color = 'red';
    }

    // Update strength bar and text
    const strengthPercent = (strength / 4) * 100;
    strengthFill.style.width = strengthPercent + '%';

    if (strength === 0) {
    strengthFill.style.backgroundColor = 'red';
    strengthText.textContent = 'Very Weak';
    strengthText.style.color = 'red';
} else if (strength === 1) {
    strengthFill.style.backgroundColor = 'orange';
    strengthText.textContent = 'Weak';
    strengthText.style.color = 'orange';
} else if (strength === 2) {
    strengthFill.style.backgroundColor = 'gold';
    strengthText.textContent = 'Moderate';
    strengthText.style.color = 'gold';
} else if (strength === 3) {
    strengthFill.style.backgroundColor = '#5bc0de';
    strengthText.textContent = 'Strong';
    strengthText.style.color = '#5bc0de';
} else if (strength === 4) {
    strengthFill.style.backgroundColor = 'green';
    strengthText.textContent = 'Very Strong';
    strengthText.style.color = 'green';
}

    // Enable submit if strong and match
    if (strength === 4 && password === confirmPassword) {
        submitButton.removeAttribute('disabled');
    } else {
        submitButton.setAttribute('disabled', 'disabled');
    }
}
</script>

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

</body>
</html>

            
            <?php
        } else {
            header("Location: forgotpassword.php?error=Email+not+found");
            exit;
        }
    } elseif (isset($_GET['token']) &&!empty($_POST["new_password"]) &&!empty($_POST["confirm_new_password"])) {
        $reset_token = $_GET['token'];
        $new_password = $_POST["new_password"];
        $confirm_new_password = $_POST["confirm_new_password"];

        if ($reset_token === $_SESSION['reset_token']) {
            $username = $_SESSION['reset_username'];
            if ($new_password === $confirm_new_password) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password =? WHERE username =?");
                $stmt->bind_param("ss", $hashed_password, $username);
                if ($stmt->execute()) {
                    unset($_SESSION['reset_token']);
                    unset($_SESSION['reset_username']);
                    header("Location: userlogin.php?success=true&message=Password+updated+successfully");
                    exit;
                } else {
                    echo "Error updating password: ". $stmt->error;
                }
            } else {
                echo "New password and confirm password do not match";
            }
        } else {
            echo "Invalid reset token";
        }
    } else {
        header("Location: forgotpassword.php?error=Invalid+form+submission");
        exit;
    }
} else {
    header("Location: forgotpassword.php?error=Invalid+request+method");
    exit;
}
?>
