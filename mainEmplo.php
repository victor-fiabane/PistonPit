<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_MainMenu.css">
    <title>Menu Funcionário</title>
</head>
<body>
    <?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    require_once 'auth.php';
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    ?>
<header class="main-header">
        <nav>
            <a href="lista_client.php">Clientes</a>
            <a href="financa.php">Financeiro</a>
            <a href="perfil_client.php">Perfil</a>
            <a href="logout.php">Sair</a>
        </nav>
</header>
    <div id="containerMain">
        <div id="mainPc">
            <div id="title">

            </div>
            <div id="container">
                <div id="menu">

                <!-- CALENDARIO -->


                </div>

            </div>

            <?php 



            ?>
        </div>
    </div>
    <div id="containerMobile">
        <div id="title">
        
        </div>
        <div id="container">
            <div id="menu">

            <!-- CALENDARIO -->

            </div>

        </div>
</body>
</html>