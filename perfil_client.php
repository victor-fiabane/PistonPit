<?php
include_once("./db/config.php");
include_once("./db/setDb.php");
include_once("./crud.php");
require_once 'auth.php';
$crud = new Crud($db);
$mensagem = "";
$sucesso = "";
if (isset($_POST['salvarButton'])) {
	$nome = isset($_POST['nomeText']) ? trim($_POST['nomeText']) : "";
	$email = isset($_POST['emailText']) ? trim($_POST['emailText']) : "";
	$telefone = isset($_POST['telefoneText']) ? trim($_POST['telefoneText']) : "";
	$senha = isset($_POST['senhaText']) ? $_POST['senhaText'] : "";

	if ($nome == "" || $email == "" || $telefone == "") {
		$mensagem = "Preencha o nome, o e-mail e o telefone.";
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$mensagem = "Informe um e-mail válido.";
	} else if ($crud->existsCliEmail($email, $_SESSION['id'])) {
		$mensagem = "Este e-mail já está sendo usado por outro cliente.";
	} else {
		$salvou = $crud->updateCliPerfil($_SESSION['id'], $nome, $email, $telefone);
		if ($salvou && $senha != "") {
			$salvou = $crud->updateCliSenha($_SESSION['id'], $senha);
		}
		if ($salvou) {
			$_SESSION['nome'] = $nome;
			$_SESSION['username'] = $email;
			$sucesso = "Dados atualizados.";
		} else {
			$mensagem = "Não foi possível salvar os dados.";
		}
	}
}
$cliente = $crud->readCliId($_SESSION['id']);
if (!$cliente) {
	header("Location: logout.php");
	exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/style_Perfil.css">
	<title>Perfil</title>
</head>
<body>
	<div id="containerPc">
		<header class="main-header">
			<nav>
				<a href="mainMenu.php">Menu</a>
				<a href="logout.php">Sair</a>
			</nav>
		</header>
		<div id="content">
			<div id="container-perfil">
				<div id="container-titulo">
					<h1>Seu perfil</h1>
				</div>

				<?php if ($sucesso != "") { ?>
					<p class="mensagem sucesso"><?php echo htmlspecialchars($sucesso); ?></p>
				<?php } ?>
				<?php if ($mensagem != "") { ?>
					<p class="mensagem erro"><?php echo htmlspecialchars($mensagem); ?></p>
				<?php } ?>
				<div id="container-dados">
					<p>Nome:
						<?php echo htmlspecialchars($cliente['cli_nome']); ?>
					</p>
					<p>CPF:
						<?php echo htmlspecialchars($cliente['cli_cpf']); ?>
					</p>
					<p>Situação:
						<?php echo ($cliente['cli_ativo'] == 1) ? "Ativo" : "Inativo"; ?>
					</p>
					<p>Termo aceito em:
						<?php echo $cliente['cli_termo_data'] ? date("d/m/Y H:i", strtotime($cliente['cli_termo_data'])) : "-"; ?>
					</p>
				</div>
				<form method="post" id="container-form">
					<p>Nome</p>
					<input type="text" name="nomeText" class="inputText" value="<?php echo htmlspecialchars($cliente['cli_nome']); ?>" required>
					<p>E-mail</p>
					<input type="email" name="emailText" class="inputText" value="<?php echo htmlspecialchars($cliente['cli_email']); ?>" required>
					<p>Telefone</p>
					<input type="text" name="telefoneText" class="inputText" value="<?php echo htmlspecialchars($cliente['cli_telefone']); ?>" required>
					<p>Nova senha</p>
					<input type="password" name="senhaText" class="inputText" placeholder="Deixe em branco para não alterar" autocomplete="new-password">
					<button class="botoes" type="submit" name="salvarButton">
						<p>Salvar alterações</p>
					</button>
				</form>
				<div id="container-historico">
					<h2>Histórico de corridas</h2>
				</div>
			</div>
		</div>
	</div>
</body>
</html>