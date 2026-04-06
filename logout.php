<?php
// Simple logout file
session_start();
session_unset();
session_destroy();
header('Location: /cloud_9_cafe/auth/login.php');
exit;
?>