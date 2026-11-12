<?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    require_once 'auth_emplo.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_MainMenu.css">
    <title>Menu Funcionário</title>
</head>
<body>
<header class="main-header">
        <nav>
            <a href="lista_client.php">Clientes</a>
            <a href="financa.php">Financeiro</a>
            <?php if ($_SESSION['func_perfil'] == 'ADMIN') { ?>
                <a href="emplo_cadastro.php">Funcionários</a>
            <?php } ?>
            <a href="perfil_emplo.php">Perfil</a>
            <a href="logout.php">Sair</a>
        </nav>
</header>
    <div id="containerMain">
        <div id="mainPc">
            <div id="title">
                <h1 class="titleText">Olá, seja bem-vindo!</h1>
            </div>
            <div id="container">
                <div id="menu">

                <!-- CALENDARIO -->

                </div>
            </div>
        </div>
    </div>
</body>
</html>