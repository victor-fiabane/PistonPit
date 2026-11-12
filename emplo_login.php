<?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (isset($_SESSION['id']) && $_SESSION['perfil'] == "funcionario") {
        header("Location: mainEmplo.php");
        exit();
    }
    $mensagem = "";
    if (isset($_POST['subButton'])){
        $user = isset($_POST['loginText']) ? trim($_POST['loginText']) : "";
        $pass = isset($_POST['passwordText']) ? $_POST['passwordText'] : "";

        if($user != "" && $pass != ""){
            $crud = new Crud($db);
            $row = $crud->readEmplo($user);

            if ($row && password_verify($pass, $row['func_senha'])) {
                if ($row['func_ativo'] == 1) {
                    $_SESSION['id'] = $row['id_func'];
                    $_SESSION['username'] = $row['func_email'];
                    $_SESSION['nome'] = $row['func_nome'];
                    $_SESSION['perfil'] = "funcionario";
                    $_SESSION['func_perfil'] = $row['func_perfil'];
                    header("Location: mainEmplo.php");
                    exit();
                } else {
                    $mensagem = "Sua conta está inativa, procure o administrador.";
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
    <title>Acesso Funcionário</title>
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body>
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
                    <a href="client_login.php">Acesso Cliente</a>
                </div>
                </div>
            </div>
        </div>
    </form>
</body>
</html>