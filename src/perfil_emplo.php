<?php
include_once(__DIR__ . "/../db/config.php");
include_once(__DIR__ . "/crud.php");
require_once(__DIR__ . "/auth_emplo.php");
$crud = new Crud($db);
$mensagem = "";
$sucesso = "";
if (isset($_POST['salvarButton'])) {
	$nome = isset($_POST['nomeText']) ? trim($_POST['nomeText']) : "";
	$senha = isset($_POST['senhaText']) ? $_POST['senhaText'] : "";
	if ($nome == "") {
		$mensagem = "Preencha o nome.";
	} else {
		$salvou = $crud->updateEmploNome($_SESSION['id'], $nome);
		if ($salvou && $senha != "") {
			$salvou = $crud->updateEmploSenha($_SESSION['id'], $senha);
		}
		if ($salvou) {
			$_SESSION['nome'] = $nome;
			$sucesso = "Dados atualizados.";
		} else {
			$mensagem = "Não foi possível salvar os dados.";
		}
	}
}
$funcionario = $crud->readEmploId($_SESSION['id']);
if (!$funcionario) {
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
	<title>Perfil Funcionário</title>
</head>
<body>
	<div id="containerPc">
		<header class="main-header">
			<nav>
				<a href="mainEmplo.php">Menu</a>
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
						<?php echo htmlspecialchars($funcionario['func_nome']) ?>
					</p>
					<p>E-mail:
						<?php echo htmlspecialchars($funcionario['func_email']) ?>
					</p>
					<p>Perfil:
						<?php echo htmlspecialchars($funcionario['func_perfil']) ?>
					</p>
				</div>
				<form method="post" id="container-form">
					<p>Nome</p>
					<input type="text" name="nomeText" class="inputText" value="<?php echo htmlspecialchars($funcionario['func_nome']); ?>" required>
					<p>Nova senha</p>
					<input type="password" name="senhaText" class="inputText" placeholder="Deixe em branco para não alterar" autocomplete="new-password">
					<button class="botoes" type="submit" name="salvarButton">
						<p>Salvar alterações</p>
					</button>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
