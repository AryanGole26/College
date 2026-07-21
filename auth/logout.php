<?php
// auth/logout.php
require_once '../config/db.php';

$_SESSION = [];
session_destroy();

header("Location: login.php");
exit();
?>