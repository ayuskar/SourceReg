<?php
// Start session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "secure_reg";

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function checkRateLimit($action, $limit = 5, $timeWindow = 300) { // Changed $timeWindow to 300 (5 minutes)
    $ip = $_SERVER['REMOTE_ADDR'];
    $key = "rate_limit_{$action}_{$ip}";
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => 1,
            'first_attempt' => time()
        ];
        return true; // No rate limit exceeded
    } else {
        $_SESSION[$key]['attempts']++;
        
        if ($_SESSION[$key]['attempts'] > $limit) {
            $elapsed = time() - $_SESSION[$key]['first_attempt'];
            if ($elapsed < $timeWindow) {
                $remaining = ceil(($timeWindow - $elapsed) / 60); // Calculate remaining minutes
                return [
                    'error' => true,
                    'message' => "Too many attempts. Please try again in {$remaining} minutes."
                ];
            } else {
                // Reset counter if time window has passed
                $_SESSION[$key] = [
                    'attempts' => 1,
                    'first_attempt' => time()
                ];
                return true; // No rate limit exceeded
            }
        } else {
            return true; // No rate limit exceeded
        }
    }
}


?>
