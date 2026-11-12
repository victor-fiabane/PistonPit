<?php
    include_once("./db/config.php");
    include_once("./db/setDb.php");
    include_once("./crud.php");
    require_once "auth_emplo.php";
    if ($_SESSION['func_perfil'] != 'ADMIN') {
        header("Location: mainEmplo.php");
        exit();
    }
    $mensagem = "";
    $sucesso = "";
    if (isset($_POST['subButton'])){
        $nome     = isset($_POST['nomeText']) ? trim($_POST['nomeText']) : "";
        $email    = isset($_POST['loginText']) ? trim($_POST['loginText']) : "";
        $senha    = isset($_POST['passwordText']) ? $_POST['passwordText'] : "";
        $confirma = isset($_POST['passwordConfirmText']) ? $_POST['passwordConfirmText'] : "";
        $perfil   = isset($_POST['perfilSelect']) ? $_POST['perfilSelect'] : "financeiro";
        $crud = new Crud($db);
        if ($nome == "" || $email == "" || $senha == "") {
            $mensagem = "Preencha todos os campos.";
        } else if ($senha != $confirma) {
            $mensagem = "As senhas informadas não são iguais.";
        } else if ($crud->existsEmplo($email)) {
            $mensagem = "Este e-mail já está cadastrado para um funcionário.";
        } else {
            if ($crud->createEmplo($nome, $email, $senha, ($perfil == "admin"))) {
                $sucesso = "Funcionário cadastrado.";
            } else {
                $mensagem = "Não foi possível concluir o cadastro, tente novamente.";
            }
        }
    }
    $crud = new Crud($db);
    $funcionarios = $crud->listEmplo();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionário</title>
    <link rel="stylesheet" href="css/style_login.css">
</head>
<body class="telaCadastro">
<header class="main-header">
        <nav>
            <a href="mainEmplo.php">Menu</a>
            <a href="logout.php">Sair</a>
        </nav>
</header>
    <form id="formMainPc" method="post">
        <div id="mainPc">
            <div id="title">
                <nobr>
                    <h1 class="titleText" id="mainTitle">Funcionários</h1> </br>
                    <h2 class="titleText" id="subtitle">Área administrativa</h2>
                </nobr>
            </div>
            <div id="inputs">
                <div id="inputsSubdiv">
                    <p id="warning" >Cadastro liberado apenas para o administrador</p>
                    <?php if ($sucesso != "") { ?>
                        <p class="mensagem sucesso"><?php echo htmlspecialchars($sucesso); ?></p>
                    <?php } ?>
                    <?php if ($mensagem != "") { ?>
                        <p class="mensagem erro"><?php echo htmlspecialchars($mensagem); ?></p>
                    <?php } ?>
                    <div class="textarea" id="textarea1">
                    <p>Nome</p>
                        <input type="text" name="nomeText" class="inputText" placeholder="Nome do funcionário" required autofocus>
                    </div>
                    <div class="textarea">
                        <p>E-mail</p>
                        <input type="text" name="loginText" class="inputText" placeholder="E-mail de acesso" required>
                    </div>
                    <div class="textarea">
                        <p>Perfil</p>
                        <select name="perfilSelect" class="inputText">
                            <option value="financeiro">Financeiro</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                    <div class="textarea">
                        <p>Senha</p>
                        <input type="password" name="passwordText" class="inputText" placeholder="Digite a senha" required>
                    </div>
                    <div class="textarea">
                        <p>Confirmar senha</p>
                        <input type="password" name="passwordConfirmText" class="inputText" placeholder="Repita a senha" required>
                    </div>
                    <div id="submitDiv">
                        <input type="submit" id="subButton" name="subButton" value="Cadastrar">
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>
</html>
