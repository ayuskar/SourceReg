<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>


<header>
    <nav class="nav-bar">
        <div class="logo">SecureReg</div>
        <ul class="nav-links nav-links-centered">
            <li><a href="http://localhost/SourceReg/" class="nav-link">Home</a></li>

            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li><a href="profile.php" class="nav-link">Profile</a></li>
                <li><a href="logout.php" class="nav-link">Logout</a></li>
            <?php else: ?>
                <li><a href="userregister.php" class="nav-link">Register</a></li>
                <li><a href="userlogin.php" class="nav-link">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
