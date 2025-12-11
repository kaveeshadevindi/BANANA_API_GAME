<?php
session_start();
session_destroy();

// Set a cute message for the redirect
$_SESSION['logout_message'] = "👋 Farewell,Come back soon! ✨";
header('Location: index.php?logout=1');
exit();
?>