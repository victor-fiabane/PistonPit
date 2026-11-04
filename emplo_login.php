<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Funcionário</title>
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body>
<!--
<?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
?>
-->

<header class="main-header">
        <nav>
            <a href="conheca.php">Conheça o kartódromo</a>
            <a href="pistas.php">Nossas pistas</a>
        </nav>
    </header>


<!--              Desktop HTML                 -->
    <form id="formMainPc" method="post">
        <div id="mainPc">
            <!-- title div -->
            <div id="title">
                <nobr>
                    <h1 class="titleText" id="mainTitle">Piston Pit</h1> </br>
                    <h2 class="titleText" id="subtitle">Kartodromo</h2>
                </nobr>
            </div>
            <!--input div -->
            <div id="inputs">
                <div id="inputsSubdiv">
                    <p id="warning" >Atenção, área de acesso Administrativa</p>
                    <br>
                    <div class="textarea" id="textarea1">
                    <p id="login">Login</p>
                        <input type="text" name="loginText" id="login" class="inputText" placeholder="Insira seu E-mail" required autofocus>
                    </div>
                    <div class="textarea">
                        <p id="pass">Senha</p>
                        <input type="password" name="passwordText" id="password" class="inputText" placeholder="Insira sua Senha" required>
                    </div>
                    <div id="submitDiv">
                        <input type="submit" id="subButton" name="subButton">
                    </div>
                </div>
            </div>
            <?php # php openning
            if (isset($_POST['subButton'])){           #
                if($_POST['loginText'] != ""){         # Checking if the textboxes are filled
                    if($_POST['passwordText'] != ""){  #
                        #write the code here
                        
                        $crud = new Crud($db);
                        $result = $crud->read();

                        $user = $_POST['loginText'];
                        $pass = $_POST['passwordText'];

                        if ($result) {
                            $found = false;
                            // Use um loop para iterar sobre os resultados obtidos com o PDO
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $login = $row['name'];
                                $senha = $row['password'];
                                    
                                if ($login == $user && $senha == $pass) {
                                    if (session_status() !== PHP_SESSION_ACTIVE) {
                                        session_start();
                                    }
                                    $_SESSION['username'] = $login;
                                    $found = true;
                                    echo '<script type="text/javascript"> window.location.href="mainMenu.php"; </script>';
                                    echo "found";
                                    break;
                                }
                            }
                            if (!$found) {
                                echo '<script>alert("E-mail ou senha incorreto, tente novamente.");</script>';
                            } else {
                                // Trate o caso em que a consulta não retornou resultados
                                echo "Funcionário não cadastrado.";
                            }
                        }
                    }


                }                                                             # Popping a message if 
            }                                                                #
            

            ?> <!-- php closing -->


        </div>
    </form>

<!---              Mobile HTML-                -->
<form id="mainMobile" method="post">
    <div id="title">
                <nobr>
                    <h1 class="titleText" id="mainTitle">Piston Pit</h1> </br>
                    <h2 class="titleText" id="subtitle">Kartodromo</h2>
                </nobr>
            </div>
            <!--input div -->
            <div id="inputs">
                <div id="inputsSubdiv">
                    <div class="textarea" id="textarea1">
                    <p id="warning" >Atenção, área Administrativa</p>
                    <p id="login">Login</p>
                        <input type="text" name="loginText" id="login" class="inputText" placeholder="Insira seu E-mail" required>
                    </div>
                    <div class="textarea">
                        <p id="pass">Senha</p>
                        <input type="text" name="passwordText" id="password" class="inputText" placeholder="Insira sua Senha" required>
                    </div>
                    <div id="submitDiv">
                        <input type="submit" id="subButton" name="subButton">
                    </div>
                </div>
        </div>    
        <?php # php openning
            if (isset($_POST['subButton'])) {           #
                if ($_POST['loginText'] != "") {         # Checking if the textboxes are filled
                    if ($_POST['passwordText'] != "") {  #
                        #write the code here

                        $crud = new Crud($db);
                        $result = $crud->read();

                        $user = $_POST['loginText'];
                        $pass = $_POST['passwordText'];

                        if ($result) {
                            $found = false;
                            // Use um loop para iterar sobre os resultados obtidos com o PDO
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $login = $row['name'];
                                $senha = $row['password'];
                                $admin = $row['isAdmin'];
                                $id = $row['userId'];
                                if ($login == $user && $senha == $pass) {
                                    session_start();
                                    $_SESSION["id"] = $id;
                                    $_SESSION["login"] = $login;
                                    $_SESSION["admin"] = $admin;

                                    $found = true;
                                    echo '<script type="text/javascript"> window.location.href="mainMenu.php"; </script>';

                                    echo "found";
                                    break;
                                }
                            }
                            if (!$found) {
                                echo '<script>alert("E-mail ou senha incorreto, tente novamente.");</script>';
                            } else {
                                // Trate o caso em que a consulta não retornou resultados
                                echo "Funcionário não cadastrado.";
                            }
                        }
                    }
                }                                                             # Popping a message if 
            }                                                                 #


            ?> <!-- php closing -->


        </div>
    </form>
</body>
</html>