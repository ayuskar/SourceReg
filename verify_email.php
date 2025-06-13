<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "secure_reg";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

// ...

if (isset($_GET['token']) && isset($_GET['user_id']) && filter_var($_GET['user_id'], FILTER_VALIDATE_INT) && $_GET['user_id'] > 0) {
    $token = $_GET['token'];
    $user_id = $_GET['user_id'];

    $stmt = $conn->prepare("SELECT verification_token FROM users WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->bind_result($stored_token);
    if ($stmt->fetch()) {
        if ((string)$token == (string)$stored_token) { // Using == for comparison
            // Verify the user
            $updateStmt = $conn->prepare("UPDATE users SET verified = 1, verification_token = NULL WHERE id = ?");
            if (!$updateStmt) {
                die("Prepare update failed: " . $conn->error);
            }
            $updateStmt->bind_param("i", $user_id);
            if (!$updateStmt->execute()) {
                die("Update execute failed: " . $updateStmt->error);
            }
            $updateStmt->close();

            header("Location: userlogin.php?success=" . urlencode("Email verified successfully. You can now log in."));
            exit;
        } else {
            header("Location: userlogin.php?error=" . urlencode("Invalid verification token."));
            exit;
        }
    } else {
        header("Location: userlogin.php?error=" . urlencode("User not found or invalid token."));
        exit;
    }
    $stmt->close();
} else {
    header("Location: userlogin.php?error=" . urlencode("Invalid request."));
    exit;
}
$conn->close();

?>
