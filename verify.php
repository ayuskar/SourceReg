<?php
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

// Retrieve and sanitize username from query string
if (isset($_GET["username"])) {
    $username = $_GET["username"];

    // **Verify user**
    $stmt = $conn->prepare("UPDATE users SET verified = 1 WHERE username =?");
    if ($stmt) {
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            echo "Your account has been successfully verified. You can now <a href='index.html'>log in</a>.";
        } else {
            echo "Error verifying account: ". $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: ". $conn->error;
    }
} else {
    echo "Invalid verification request.";
}

$conn->close();
?>
