<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}
if (!isset($_SESSION["id"]) || $_SESSION["perfil"] != "funcionario") {
	header("Location: emplo_login.php");
	exit();
}
include_once(__DIR__ . "/../db/config.php");
include_once(__DIR__ . "/crud.php");
$crudAuth = new Crud($db);
$funcAtual = $crudAuth->readEmploId($_SESSION["id"]);
if (!$funcAtual || $funcAtual["func_ativo"] != 1) {
	$_SESSION = array();
	session_destroy();
	header("Location: emplo_login.php");
	exit();
}
$_SESSION["func_perfil"] = $funcAtual["func_perfil"];
$_SESSION["nome"] = $funcAtual["func_nome"];
?>
