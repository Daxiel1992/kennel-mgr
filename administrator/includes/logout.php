<?php
session_start();
require '../../includes/dbConfig.php';
$userid = mysqli_real_escape_string($db, $_SESSION['user_id']);
$db->query("UPDATE `users` SET `login_token` = '' WHERE `users`.`id` = $userid");
$_SESSION = array();
session_destroy();
header('Location: ../index.php');
?>
