<?php
require_once 'PHPMailer/send.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "secure_reg";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

if (
    isset($_POST["full_name"]) && 
    isset($_POST["username"]) && 
    isset($_POST["email"]) && 
    isset($_POST["password"])
) {
    $full_name = trim($_POST["full_name"]);
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Password strength checks
    $passwordStrengthErrors = [];
    if (strlen($password) < 6) {
        $passwordStrengthErrors[] = "Minimum 6 characters please";
    }
    if (!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password)) {
        $passwordStrengthErrors[] = "Uppercase & Lowercase characters required";
    }
    if (!preg_match("/\d/", $password)) {
        $passwordStrengthErrors[] = "At least one number required";
    }
    if (!preg_match("/[^A-Za-z0-9]/", $password)) {
        $passwordStrengthErrors[] = "At least one special character required";
    }

    if (!empty($passwordStrengthErrors)) {
        $errorMsg = urlencode("Password too weak: " . implode(", ", $passwordStrengthErrors));
        header("Location: userregister.php?error=$errorMsg");
        exit;
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: userregister.php?error=" . urlencode("Username already exists"));
        exit;
    }
    $stmt->close();

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: userregister.php?error=" . urlencode("Email already exists"));
        exit;
    }
    $stmt->close();

    // Hash and insert user
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$verified = 0; // Assign 0 to a variable
$stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, verified) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssssi", $full_name, $username, $email, $hashed_password, $verified); 
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id; // Get the newly inserted user ID
        $stmt->close();

        // Generate verification token and store it in the database
        $verification_token = bin2hex(random_bytes(16));
        $stmt = $conn->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
        $stmt->bind_param("si", $verification_token, $user_id);
        $stmt->execute();
        $stmt->close();

        // Send verification email to the user
        sendVerificationEmail($email, $username, $verification_token, $user_id);

        header("Location: userlogin.php?success=" . urlencode("Registration successful. Please verify your email address."));
        exit;
        } else {
            $stmt->close();
            header("Location: userregister.php?error=" . urlencode("Error inserting user"));
            exit;
        }
    } else {
        header("Location: userregister.php?error=" . urlencode("Error preparing insert statement"));
        exit;
    }
} else {
    header("Location: userregister.php?error=" . urlencode("Please fill out all fields"));
    exit;
}
$conn->close();
?>
