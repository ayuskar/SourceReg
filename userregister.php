<?php
require_once 'db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Already logged in, redirect to index
    header("Location: index.php");
    exit;
}
$rateLimitCheck = checkRateLimit('register');

if (is_array($rateLimitCheck) && $rateLimitCheck['error']) {
    // Display rate limit error message
    $rateLimitError = $rateLimitCheck['message'];
} else {
    // No rate limit exceeded, proceed with registration
    $rateLimitError = null;
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
     <style>
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
  #password-requirements {
    padding: 0;
    list-style: none;
}
#password-requirements li {
    margin-bottom: 5px;
    color: red; /* default text color */
}
.valid {
     background-color: rgb(48, 47, 47);
}
.requirement-status::before {
    content: "\2718"; /* ✘ */
    color: red;
}
li.valid .requirement-status::before {
    content: "\2714"; /* ✓ */
    color: green;
}
</style>
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
                <?php if ($rateLimitError):?>
    <div class="alert alert-danger" role="alert">
        <?= $rateLimitError?>
    </div>
<?php endif;?>
<?php if (isset($_GET['error']) && strpos($_GET['error'], 'CAPTCHA') !== false): ?>
            <div class="error-message" style="color:red;"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
    <div class="error" style="color: red;">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="success" style="color: green;">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>

            </div>
            <div style="background-color: rgb(48, 47, 47);" class="register-form-container">
                <form id="register-form" method="post" action="newuser.php">
                    <h2>Register</h2>
                    <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter full name" required>
                </div>
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter email" required></div>
               <div class="form-group">
  <label for="password">Password:</label>
  <div class="password-wrapper">
    <input type="password" id="password" name="password" placeholder="Enter password" required oninput="checkPasswordStrength(this.value)">
    <span class="toggle-password" onclick="togglePassword('password', this)">
      <i class="fa-solid fa-eye"></i>
    </span>
  </div>

  <!-- Strength bar container -->
  <div id="strength-bar" style="height: 8px; background-color: #ccc; border-radius: 5px; margin-top: 6px;">
    <div id="strength-fill" style="height: 8px; width: 0%; background-color: red; border-radius: 5px;"></div>
  </div>

  <!-- Optional text indicator -->
<div id="password-strength-text" style="margin-top: 5px; font-weight: bold; text-align: center;"></div>
</div>

<script>
  
</script>

  
<div>
    <div id="strength-bar">
        <div id="strength-fill"></div>
    </div>
    
    <ul id="password-requirements">
        <li id="length"><a style='color:white'>Minimum 6 characters</a> <span class="requirement-status"></span></li>
        <li id="uppercase"><a style='color:white'>Uppercase & Lowercase characters <span class="requirement-status"></span></li>
        <li id="number"><a style='color:white'>At least one number <span class="requirement-status"></span></li>
        <li id="special"><a style='color:white'>At least one special character <span class="requirement-status"></span></li>
    </ul>
</div>
<script>
    // Update the requirement statuses based on the password strength
    document.getElementById("password").addEventListener("input", function () {
        const password = this.value;
        const lengthReq = document.getElementById("length");
        const upperLowerReq = document.getElementById("uppercase");
        const numberReq = document.getElementById("number");
        const specialReq = document.getElementById("special");

        if (password.length >= 6) {
            lengthReq.classList.add("valid");
        } else {
            lengthReq.classList.remove("valid");
        }

        if (/[A-Z]/.test(password) && /[a-z]/.test(password)) {
            upperLowerReq.classList.add("valid");
        } else {
            upperLowerReq.classList.remove("valid");
        }

        if (/\d/.test(password)) {
            numberReq.classList.add("valid");
        } else {
            numberReq.classList.remove("valid");
        }

        if (/[^A-Za-z0-9]/.test(password)) {
            specialReq.classList.add("valid");
        } else {
            specialReq.classList.remove("valid");
        }
    });
    function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    const iconEl = icon.querySelector('i');
    if (field.type === "password") {
      field.type = "text";
      iconEl.classList.remove("fa-eye");
      iconEl.classList.add("fa-eye-slash");
    } else {
      field.type = "password";
      iconEl.classList.remove("fa-eye-slash");
      iconEl.classList.add("fa-eye");
    }
  }

  function checkPasswordStrength(password) {
    const fill = document.getElementById('strength-fill');
    const text = document.getElementById('password-strength-text');

    const hasLower = /[a-z]/.test(password);
    const hasUpper = /[A-Z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSymbol = /[^A-Za-z0-9]/.test(password);

    const strengthScore = [hasLower, hasUpper, hasNumber, hasSymbol].filter(Boolean).length;

    let width = '0%';
    let color = 'red';
    let strengthText = 'Too short';

    if (password.length < 6) {
      width = '10%';
      color = 'gray';
    } else {
      switch (strengthScore) {
        case 1:
          width = '33%';
          color = 'red';
          strengthText = 'Weak';
          break;
        case 2:
          width = '66%';
          color = 'orange';
          strengthText = 'Moderate';
          break;
        case 3:
        case 4:
          width = '100%';
          color = 'green';
          strengthText = 'Strong';
          break;
      }
    }

    fill.style.width = width;
    fill.style.backgroundColor = color;
    text.textContent = strengthText;
    text.style.color = color;
  }
    if (window.location.search.includes('error') || window.location.search.includes('success')) {
    if (history.replaceState) {
      const cleanUrl = window.location.origin + window.location.pathname;
      history.replaceState(null, null, cleanUrl);
    }
  }
</script>


<div class="form-group">
  <label for="confirm-password">Confirm Password:</label>
  <div class="password-wrapper">
    <input type="password" id="confirm-password" name="confirm-password" placeholder="Re-enter password" required>
    <span class="toggle-password" onclick="togglePassword('confirm-password', this)">
      <i class="fa-solid fa-eye"></i>
    </span>
  </div>
</div>

                 

                    <div id="recaptcha-container" required>
                        <div class="g-recaptcha" data-sitekey="6LfL-VArAAAAAFfliextCHsZJP34Swlilz2RKCnv" required></div>
                        <div id="recaptcha-error"></div>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Register">
                    </div>
                    <div id="registration-status"></div>
                </form>
            </div>
        </section>
    </main>

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
    <script src="script2.js"></script>
    

</body>
</html>
