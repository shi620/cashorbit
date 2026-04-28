<?php
// =============================================
// CASHORBIT - ADMIN LOGOUT
// =============================================
session_start();
session_destroy();
header("Location: login.php");
exit();
?>
