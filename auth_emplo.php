<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
if (!isset($_SESSION["id"]) || $_SESSION["perfil"] != "funcionario") {
	header("Location: emplo_login.php");
	exit();
}
?>