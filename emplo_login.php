<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Funcionário</title>
    <link rel="stylesheet" href="css/style_login.css">
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
            <a href="conheca.php">Conheça o kartódromo</a>
            <a href="pistas.php">Nossas pistas</a>
        </nav>
    </header>
    <form id="formMainPc" method="post">
        <div id="mainPc">
            <div id="title">
                <nobr>
                    <h1 class="titleText" id="mainTitle">Piston Pit</h1> </br>
                    <h2 class="titleText" id="subtitle">Kartodromo</h2>
                </nobr>
            </div>
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
            <?php
            if (isset($_POST['subButton'])){        
                if($_POST['loginText'] != ""){    
                    if($_POST['passwordText'] != ""){        
                        $crud = new Crud($db);
                        $result = $crud->readEmplo();
                        $user = $_POST['loginText'];
                        $pass = $_POST['passwordText'];
                        if ($result) {
                            $found = false;
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $login = $row['func_email'];
                                $senha = $row['func_senha'];
                                $ativo = $row['func_ativo'];
                                if ($ativo == 1) { 
                                        if (session_status() !== PHP_SESSION_ACTIVE) {
                                            session_start();
                                        }
                                        $_SESSION['id'] = $row['id_func'];
                                        $_SESSION['username'] = $login;
                                        $found = true;
                                        echo '<script type="text/javascript"> window.location.href="mainEmplo.php"; </script>';
                                        echo "found";
                                        break;
                                    } else {
                                        echo '<script>alert("Sua conta está inativa.");</script>';
                                        $found = true;
                                        break;
                                    }
                                }
                            if (!$found) {
                                echo '<script>alert("E-mail ou senha incorreto, tente novamente.");</script>';
                            } else {
                                echo "Funcionário não cadastrado.";
                            }
                        }
                    }
                }      
            }                                       
            ?> 
        </div>
    </form>
<form id="mainMobile" method="post">
    <div id="title">
                <nobr>
                    <h1 class="titleText" id="mainTitle">Piston Pit</h1> </br>
                    <h2 class="titleText" id="subtitle">Kartodromo</h2>
                </nobr>
            </div>
            <div id="inputs">
                <div id="inputsSubdiv">
                    <div class="textarea" id="textarea1">
                    <p id="warning" >Atenção, área Administrativa</p>
                    <p id="login">Login</p>
                        <input type="text" name="loginText" id="login" class="inputText" placeholder="Insira seu E-mail" required>
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
        <?php
            if (isset($_POST['subButton'])) {           
                if ($_POST['loginText'] != "") {      
                    if ($_POST['passwordText'] != "") {  
                        $crud = new Crud($db);
                        $result = $crud->readEmplo();
                        $user = $_POST['loginText'];
                        $pass = $_POST['passwordText'];
                        if ($result) {
                            $found = false;
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                $login = $row['func_email'];
                                $senha = $row['func_senha'];
                                $ativo = $row['func_ativo'];
                                if ($ativo == 1) { 
                                        if (session_status() !== PHP_SESSION_ACTIVE) {
                                            session_start();
                                        }
                                        $_SESSION['id'] = $row['id_func'];
                                        $_SESSION['username'] = $login;
                                        $found = true;
                                        echo '<script type="text/javascript"> window.location.href="mainEmplo.php"; </script>';
                                        echo "found";
                                        break;
                                    } else {
                                        echo '<script>alert("Sua conta está inativa.");</script>';
                                        $found = true;
                                        break;
                                    }
                                }
                            if (!$found) {
                                echo '<script>alert("E-mail ou senha incorreto, tente novamente.");</script>';
                            } else {
                                echo "Funcionário não cadastrado.";
                            }
                        }
                    }
                }    
            }                                            
            ?> 
        </div>
    </form>
</body>
</html>