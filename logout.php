<?php
    session_start();
    unset($_SESSION["id"]);
    unset($_SESSION["email"]);
    unset($_SESSION["nome"]);
    unset($_SESSION["perfil"]);
    unset($_SESSION["admin"]);
    session_destroy();
    header("location: client_login.php");
?>