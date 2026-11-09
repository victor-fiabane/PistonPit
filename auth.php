<?php
if (!isset($_SESSION["id"])) {
	header("Location: client_login.php");
	exit();
}
?>