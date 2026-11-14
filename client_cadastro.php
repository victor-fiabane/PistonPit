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
    if (isset($_POST['subButton'])){
        $nome     = isset($_POST['nomeText']) ? trim($_POST['nomeText']) : "";
        $cpf      = isset($_POST['cpfText']) ? trim($_POST['cpfText']) : "";
        $email    = isset($_POST['loginText']) ? trim($_POST['loginText']) : "";
        $telefone = isset($_POST['telefoneText']) ? trim($_POST['telefoneText']) : "";
        $senha    = isset($_POST['passwordText']) ? $_POST['passwordText'] : "";
        $confirma = isset($_POST['passwordConfirmText']) ? $_POST['passwordConfirmText'] : "";
        $termo    = isset($_POST['termoCheck']);
        $crud = new Crud($db);
        if ($nome == "" || $cpf == "" || $email == "" || $telefone == "" || $senha == "") {
            $mensagem = "Preencha todos os campos.";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensagem = "Informe um e-mail válido.";
        } else if (strlen($senha) < 6) {
            $mensagem = "Informe uma senha válida.";
        } else if (!$termo) {
            $mensagem = "É necessário aceitar o termo de consentimento para finalizar o cadastro.";
        } else if ($crud->existsCli($email, $cpf)) {
            $mensagem = "Já existe um cadastro com este e-mail ou CPF.";
        } else {
            if ($crud->createCli($nome, $cpf, $email, $telefone, $senha)) {
                header("Location: client_login.php?cadastro=ok");
                exit();
            }
            $mensagem = "Não foi possível concluir o cadastro, tente novamente.";
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Cliente</title>
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body>
    <header class="main-header">
        <nav>
            <a href="client_login.php">Voltar ao login</a>
            <a href="conheca.php">Conheça o kartódromo</a>
            <a href="pistas.php">Nossas pistas</a>
        </nav>
</header>
    <form id="formMainPc" method="post">
        <div id="mainPc">
            <div id="title">
                <nobr>
                    <h1 class="titleText" id="mainTitle">Criar conta</h1> </br>
                    <h2 class="titleText" id="subtitle">Piston Pit</h2>
                </nobr>
            </div>
            <div id="inputs">
                <div id="inputsSubdiv">
                    <?php if ($mensagem != "") { ?>
                        <p class="mensagem erro"><?php echo htmlspecialchars($mensagem); ?></p>
                    <?php } ?>
                    <div class="textarea" id="textarea1">
                    <p>Nome</p>
                        <input type="text" name="nomeText" class="inputText" placeholder="Insira seu nome" required autofocus>
                    </div>
                    <div class="textarea">
                        <p>CPF</p>
                        <input type="text" name="cpfText" class="inputText" placeholder="Somente números" maxlength="14" required>
                    </div>
                    <div class="textarea">
                        <p>Telefone</p>
                        <input type="text" name="telefoneText" class="inputText" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="textarea">
                        <p>E-mail</p>
                        <input type="text" name="loginText" class="inputText" placeholder="Insira seu e-mail" required>
                    </div>
                    <div class="textarea">
                        <p>Senha</p>
                        <input type="password" name="passwordText" class="inputText" placeholder="Insira uma senha" required>
                    </div>
                    <br>
                    <div class="termo">
                        <label>
                            <input type="checkbox" name="termoCheck">
                            Li e aceito o termo de consentimento para o tratamento dos meus dados pessoais,
                            utilizados no cadastro e na participação das corridas do kartódromo.
                        </label>
                    </div>
                    <div id="submitDiv">
                        <input type="submit" id="subButton" name="subButton" value="Cadastrar">
                    </div>
                <div id="func">
                    <a href="client_login.php">Já tenho cadastro</a>
                </div>
                </div>
            </div>
        </div>
    </form>
</body>
</html>