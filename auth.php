<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
if (!isset($_SESSION["id"]) || $_SESSION["perfil"] != "cliente") {
	header("Location: client_login.php");
	exit();
}
include_once("./db/config.php");
include_once("./db/setDb.php");
include_once("./crud.php");
$crudAuth = new Crud($db);
$cliAtual = $crudAuth->readCliId($_SESSION["id"]);
if (!$cliAtual || $cliAtual["cli_ativo"] != 1) {
	$_SESSION = array();
	session_destroy();
	header("Location: client_login.php");
	exit();
}
$_SESSION["nome"] = $cliAtual["cli_nome"];
?>