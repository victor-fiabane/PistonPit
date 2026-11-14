<?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (isset($_SESSION['id']) && $_SESSION['perfil'] == "cliente") {
        header("Location: mainMenu.php");
        exit();
    }
    $mensagem = "";
    $sucesso = "";
    if (isset($_GET['cadastro']) && $_GET['cadastro'] == "ok") {
        $sucesso = "Cadastro realizado, faça o login para continuar.";
    }
    if (isset($_POST['subButton'])){
        $user = isset($_POST['loginText']) ? trim($_POST['loginText']) : "";
        $pass = isset($_POST['passwordText']) ? $_POST['passwordText'] : "";
        if($user != "" && $pass != ""){
            $crud = new Crud($db);
            $row = $crud->readCli($user);
            if ($row && password_verify($pass, $row['cli_senha'])) {
                if ($row['cli_ativo'] == 1) {
                    $_SESSION['id'] = $row['id_cli'];
                    $_SESSION['username'] = $row['cli_email'];
                    $_SESSION['nome'] = $row['cli_nome'];
                    $_SESSION['perfil'] = "cliente";
                    header("Location: mainMenu.php");
                    exit();
                } else {
                    $mensagem = "Sua conta está inativa, procure o kartódromo.";
                }
            } else {
                $mensagem = "E-mail ou senha incorreto, tente novamente.";
            }
        } else {
            $mensagem = "Informe o e-mail e a senha.";
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Cliente</title>
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body>
<header class="main-header">
        <nav>
            <a href="conheca.php">Conheça o kartódromo</a>
            <a href="pistas.php">Nossas pistas</a>
            <a href="client_cadastro.php">Criar conta</a>
        </nav>
</header>
    <form id="formMainPc" method="post">
        <div id="mainPc">
            <div id="title">
                <nobr>
                    <h1 class="titleText" id="mainTitle">Piston Pit</h1> </br>
                    <h2 class="titleText" id="subtitle">Kartódromo</h2>
                </nobr>
            </div>
           <div id="inputs">
                <div id="inputsSubdiv">
                    <?php if ($sucesso != "") { ?>
                        <p class="mensagem sucesso"><?php echo htmlspecialchars($sucesso); ?></p>
                    <?php } ?>
                    <?php if ($mensagem != "") { ?>
                        <p class="mensagem erro"><?php echo htmlspecialchars($mensagem); ?></p>
                    <?php } ?>
                    <div class="textarea" id="textarea1">
                    <p>Login</p>
                        <input type="text" name="loginText" id="login" class="inputText" placeholder="Insira seu E-mail" required autofocus>
                    </div>
                    <div class="textarea">
                        <p>Senha</p>
                        <input type="password" name="passwordText" id="password" class="inputText" placeholder="Insira sua Senha" required>
                    </div>
                    <div id="submitDiv">
                        <input type="submit" id="subButton" name="subButton" value="Entrar">
                    </div>
                <div id="func">
                    <a href="emplo_login.php">Acesso Restrito</a>
                </div>
                </div>
            </div>
        </div>
    </form>
</body>
</html>