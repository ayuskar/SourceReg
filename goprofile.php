<?php
session_start();
require_once 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $stmt;

        // Check if input is an email
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("SELECT password, verified FROM users WHERE email = ?");
        } else {
            $stmt = $conn->prepare("SELECT password, verified FROM users WHERE username = ?");
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($hashed_password, $verified);
            $stmt->fetch();

            // **Verification Check Inserted Here**
            if ($verified === 0) {
                header("Location: userlogin.php?error=Email+not+verified.+Please+verify+your+email+address+before+logging+in.");
                exit;
            }

            if (password_verify($password, $hashed_password)) {
                $_SESSION['loggedin'] = true;
                if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                    $stmt = $conn->prepare("SELECT username FROM users WHERE email = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($username);
                    $stmt->fetch();
                }
                $_SESSION['username'] = $username;
                header("Location: profile.php?success=true&message=Login+Successful");
                exit;
            } else {
                header("Location: userlogin.php?error=invalid_credentials");
                exit;
            }
        } else {
            header("Location: userlogin.php?error=invalid_credentials");
            exit;
        }
    } else {
        header("Location: userlogin.php?error=invalid_form");
        exit;
    }
}
?>
