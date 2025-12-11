<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'banana_game');
define('DB_USER', 'root');
define('DB_PASS', '');

// Google reCAPTCHA
define('RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI'); // Test key
define('RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe'); // Test key

// Game configuration
define('INITIAL_STAGE', 1);
define('STAGE_THRESHOLDS', [
    1 => 100,    // Stage 2 at 100 points
    2 => 300,    // Stage 3 at 300 points
    3 => 600,    // Stage 4 at 600 points
    4 => 1000,   // Stage 5 at 1000 points
    5 => 1500    // Max stage
]);

// Banana API configuration
define('BANANA_API_URL', 'https://marcconrad.com/uob/banana/api.php');

// Development mode
?>