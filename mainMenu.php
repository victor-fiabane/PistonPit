<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_MainMenu.css">
    <title>Menu</title>
</head>

<body>

    <?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    ?>

<header class="main-header">
        <nav>
            <a href="conheca.html">Conheça o kartódromo</a>
            <a href="pistas.html">Nossas pistas</a>
            <a href="perfil_client.php">Perfil</a>
            <a href="logout.php">Sair</a>
        </nav>
</header>



    <!--              Desktop HTML                 -->
    <div id="containerMain">
        <div id="mainPc">
            <!-- title div -->
            <div id="title">

            </div>
            <!--input div -->
            <div id="container">
                <div id="menu">

                <!-- CALENDARIO -->


                </div>

            </div>

            <?php # php openning



            ?> <!-- php closing -->


        </div>
    </div>

    <!---              Mobile HTML-                -->


    <div id="containerMobile">

        <!-- title div -->
        <div id="title">
        
        </div>
        <!--input div -->
        <div id="container">
            <div id="menu">

            <!-- CALENDARIO -->

            </div>

        </div>



</body>

</html>