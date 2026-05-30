<?php
session_start();
session_destroy();
header("Location: ../client/login.php?success=Logged out successfully.");
exit();
?>
