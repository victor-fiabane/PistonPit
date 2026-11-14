<?php
include_once("./db/config.php");
include_once("./db/setDb.php");
include_once("./crud.php");
require_once "auth_emplo.php";
$crud = new Crud($db);
$isAdmin = ($_SESSION['func_perfil'] == 'ADMIN');
$mensagem = "";
$tipoMensagem = "";
$idEdicao = 0;
if ($isAdmin && isset($_POST['salvar'])) {
	$idEdicao = isset($_POST['id_cli']) ? (int) $_POST['id_cli'] : 0;
	$email = isset($_POST['cli_email']) ? trim($_POST['cli_email']) : "";
	$telefone = isset($_POST['cli_telefone']) ? trim($_POST['cli_telefone']) : "";
	$ativo = (isset($_POST['cli_ativo']) && $_POST['cli_ativo'] == 1) ? 1 : 0;
	$senha = isset($_POST['cli_senha']) ? $_POST['cli_senha'] : "";
	if ($idEdicao <= 0) {
		$mensagem = "Cliente não identificado.";
		$tipoMensagem = "erro";
	} else if ($email == "" || $telefone == "") {
		$mensagem = "Informe o e-mail e o telefone.";
		$tipoMensagem = "erro";
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$mensagem = "Informe um e-mail válido.";
		$tipoMensagem = "erro";
	} else if ($crud->existsCliEmail($email, $idEdicao)) {
		$mensagem = "Já existe outro cliente cadastrado com este e-mail.";
		$tipoMensagem = "erro";
	} else {
		$salvou = $crud->updateCliAdmin($idEdicao, $email, $telefone, $ativo);
		if ($salvou && $senha != "") {
			$salvou = $crud->updateCliSenha($idEdicao, $senha);
		}
		if ($salvou) {
			header("Location: lista_client.php?salvo=1");
			exit();
		} else {
			$mensagem = "Não foi possível salvar as alterações.";
			$tipoMensagem = "erro";
		}
	}
}
if ($isAdmin && $idEdicao == 0 && isset($_GET['edit'])) {
	$idEdicao = (int) $_GET['edit'];
}
$clienteEdicao = null;
if ($isAdmin && $idEdicao > 0) {
	$clienteEdicao = $crud->readCliId($idEdicao);
	if (!$clienteEdicao) {
		$idEdicao = 0;
		$mensagem = "Cliente não encontrado.";
		$tipoMensagem = "erro";
	}
}
$clientes = $crud->listCli();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_Perfil.css">
    <title>Listagem de Cliente</title>
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
					<h1>Clientes cadastrados</h1>
					<p><?php echo count($clientes); ?> registro(s)</p>
				</div>

				<?php if (isset($_GET['salvo'])) { ?>
					<p class="mensagem sucesso">Dados do cliente atualizados com sucesso.</p>
				<?php } ?>

				<?php if ($mensagem != "") { ?>
					<p class="mensagem <?php echo $tipoMensagem; ?>"><?php echo htmlspecialchars($mensagem); ?></p>
				<?php } ?>

				<?php if ($clienteEdicao) { ?>
					<form method="post">
						<div id="container-form">
							<h2>Editando: <?php echo htmlspecialchars($clienteEdicao['cli_nome']); ?></h2>
							<input type="hidden" name="id_cli" value="<?php echo htmlspecialchars($clienteEdicao['id_cli']); ?>">
							<p>E-mail</p>
							<input type="email" name="cli_email" class="inputText" value="<?php echo htmlspecialchars(isset($_POST['cli_email']) ? $_POST['cli_email'] : $clienteEdicao['cli_email']); ?>" required autofocus>
							<p>Telefone</p>
							<input type="text" name="cli_telefone" class="inputText" value="<?php echo htmlspecialchars(isset($_POST['cli_telefone']) ? $_POST['cli_telefone'] : $clienteEdicao['cli_telefone']); ?>" required>
							<p>Situação</p>
							<select name="cli_ativo" class="inputText">
								<option value="1" <?php echo ($clienteEdicao['cli_ativo'] == 1) ? "selected" : ""; ?>>Ativo</option>
								<option value="0" <?php echo ($clienteEdicao['cli_ativo'] == 0) ? "selected" : ""; ?>>Inativo</option>
							</select>
							<p>Nova senha</p>
							<input type="password" name="cli_senha" class="inputText" placeholder="Deixe em branco para não alterar" autocomplete="new-password">
							<div id="acoes-form">
								<input type="submit" name="salvar" class="botoes" value="Salvar alterações">
								<a class="link-cancelar" href="lista_client.php">Cancelar</a>
							</div>
						</div>
					</form>
				<?php } ?>
				<div id="container-tabela">
					<table class="tabela">
						<tr>
							<th>ID</th>
							<th>Nome</th>
							<th>E-mail</th>
							<th>Telefone</th>
							<th>Situação</th>
							<th>Termo aceito em</th>
							<?php if ($isAdmin) { ?>
								<th>Ações</th>
							<?php } ?>
						</tr>
						<?php foreach ($clientes as $cliente) { ?>
							<tr <?php echo ($cliente['id_cli'] == $idEdicao) ? 'class="editando"' : ''; ?>>
								<td><?php echo htmlspecialchars($cliente['id_cli']); ?></td>
								<td><?php echo htmlspecialchars($cliente['cli_nome']); ?></td>
								<td><?php echo htmlspecialchars($cliente['cli_email']); ?></td>
								<td><?php echo htmlspecialchars($cliente['cli_telefone']); ?></td>
								<td><?php echo ($cliente['cli_ativo'] == 1) ? "Ativo" : "Inativo"; ?></td>
								<td><?php echo $cliente['cli_termo_data'] ? date("d/m/Y H:i", strtotime($cliente['cli_termo_data'])) : "-"; ?></td>
								<?php if ($isAdmin) { ?>
									<td>
										<?php if ($cliente['id_cli'] == $idEdicao) { ?>
											<a class="acao" href="lista_client.php">Fechar</a>
										<?php } else { ?>
											<a class="acao" href="lista_client.php?edit=<?php echo htmlspecialchars($cliente['id_cli']); ?>">Editar</a>
										<?php } ?>
									</td>
								<?php } ?>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
    </div>
</body>
</html>