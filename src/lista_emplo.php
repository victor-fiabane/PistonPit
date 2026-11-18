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
	$idEdicao = isset($_POST['id_func']) ? (int) $_POST['id_func'] : 0;
	$nome = isset($_POST['func_nome']) ? trim($_POST['func_nome']) : "";
	$email = isset($_POST['func_email']) ? trim($_POST['func_email']) : "";
	$perfisValidos = array('ADMIN', 'FINANCEIRO');
	$perfil = isset($_POST['func_perfil']) ? $_POST['func_perfil'] : 'FINANCEIRO';
	if (!in_array($perfil, $perfisValidos)) {
		$perfil = 'FINANCEIRO';
	}
	$ativo = (isset($_POST['func_ativo']) && $_POST['func_ativo'] == 1) ? 1 : 0;
	$senha = isset($_POST['func_senha']) ? $_POST['func_senha'] : "";
	if ($idEdicao <= 0) {
		$mensagem = "Funcionário não identificado.";
		$tipoMensagem = "erro";
	} else if ($nome == "" || $email == "") {
		$mensagem = "Informe o nome e o e-mail.";
		$tipoMensagem = "erro";
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$mensagem = "Informe um e-mail válido.";
		$tipoMensagem = "erro";
	} else if ($crud->existsEmploEmail($email, $idEdicao)) {
		$mensagem = "Já existe outro funcionário cadastrado com este e-mail.";
		$tipoMensagem = "erro";
	} else if ($idEdicao == $_SESSION['id'] && $ativo != 1) {
		$mensagem = "Você não pode inativar o seu próprio usuário.";
		$tipoMensagem = "erro";
	} else if ($idEdicao == $_SESSION['id'] && $perfil != 'ADMIN') {
		$mensagem = "Você não pode remover o seu próprio perfil de administrador.";
		$tipoMensagem = "erro";
	} else {
		$salvou = $crud->updateEmploAdmin($idEdicao, $nome, $email, $perfil, $ativo);
		if ($salvou && $senha != "") {
			$salvou = $crud->updateEmploSenha($idEdicao, $senha);
		}
		if ($salvou) {
			header("Location: lista_emplo.php?salvo=1");
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
$funcEdicao = null;
if ($isAdmin && $idEdicao > 0) {
	$funcEdicao = $crud->readEmploId($idEdicao);
	if (!$funcEdicao) {
		$idEdicao = 0;
		$mensagem = "Funcionário não encontrado.";
		$tipoMensagem = "erro";
	}
}
$funcionarios = $crud->listEmplo();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_Perfil.css">
    <title>Listagem de Funcionário</title>
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
					<h1>Funcionários cadastrados</h1>
					<p><?php echo count($funcionarios); ?> registro(s)</p>
				</div>
				<?php if (isset($_GET['salvo'])) { ?>
					<p class="mensagem sucesso">Dados do funcionário atualizados com sucesso.</p>
				<?php } ?>
				<?php if ($mensagem != "") { ?>
					<p class="mensagem <?php echo $tipoMensagem; ?>"><?php echo htmlspecialchars($mensagem); ?></p>
				<?php } ?>
				<?php if ($funcEdicao) { ?>
					<form method="post">
						<div id="container-form">
							<h2>Editando: <?php echo htmlspecialchars($funcEdicao['func_nome']); ?></h2>
							<input type="hidden" name="id_func" value="<?php echo htmlspecialchars($funcEdicao['id_func']); ?>">
							<p>Nome</p>
							<input type="text" name="func_nome" class="inputText" value="<?php echo htmlspecialchars(isset($_POST['func_nome']) ? $_POST['func_nome'] : $funcEdicao['func_nome']); ?>" required autofocus>
							<p>E-mail</p>
							<input type="email" name="func_email" class="inputText" value="<?php echo htmlspecialchars(isset($_POST['func_email']) ? $_POST['func_email'] : $funcEdicao['func_email']); ?>" required>
							<p>Perfil</p>
							<select name="func_perfil" class="inputText">
								<option value="FUNCIONARIO" <?php echo ($funcEdicao['func_perfil'] != 'ADMIN') ? "selected" : ""; ?>>Funcionário</option>
								<option value="ADMIN" <?php echo ($funcEdicao['func_perfil'] == 'ADMIN') ? "selected" : ""; ?>>Administrador</option>
							</select>
							<p>Situação</p>
							<select name="func_ativo" class="inputText">
								<option value="1" <?php echo ($funcEdicao['func_ativo'] == 1) ? "selected" : ""; ?>>Ativo</option>
								<option value="0" <?php echo ($funcEdicao['func_ativo'] == 0) ? "selected" : ""; ?>>Inativo</option>
							</select>
							<p>Nova senha</p>
							<input type="password" name="func_senha" class="inputText" placeholder="Deixe em branco para não alterar" autocomplete="new-password">
							<div id="acoes-form">
								<input type="submit" name="salvar" class="botoes" value="Salvar alterações">
								<a class="link-cancelar" href="lista_emplo.php">Cancelar</a>
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
							<th>Perfil</th>
							<th>Situação</th>
							<?php if ($isAdmin) { ?>
								<th>Ações</th>
							<?php } ?>
						</tr>
						<?php foreach ($funcionarios as $funcionario) { ?>
							<tr <?php echo ($funcionario['id_func'] == $idEdicao) ? 'class="editando"' : ''; ?>>
								<td><?php echo htmlspecialchars($funcionario['id_func']); ?></td>
								<td><?php echo htmlspecialchars($funcionario['func_nome']); ?></td>
								<td><?php echo htmlspecialchars($funcionario['func_email']); ?></td>
								<td><?php echo ($funcionario['func_perfil'] == 'ADMIN') ? "Administrador" : "Funcionário"; ?></td>
								<td><?php echo ($funcionario['func_ativo'] == 1) ? "Ativo" : "Inativo"; ?></td>
								<?php if ($isAdmin) { ?>
									<td>
										<?php if ($funcionario['id_func'] == $idEdicao) { ?>
											<a class="acao" href="lista_emplo.php">Fechar</a>
										<?php } else { ?>
											<a class="acao" href="lista_emplo.php?edit=<?php echo htmlspecialchars($funcionario['id_func']); ?>">Editar</a>
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
