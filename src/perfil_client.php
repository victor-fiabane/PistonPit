<?php
include_once(__DIR__ . "/../db/config.php");
include_once(__DIR__ . "/crud.php");
require_once(__DIR__ . "/auth.php");
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
$duracaoMin = 30;
$corte = time() - ($duracaoMin * 60);
$crud->updateCorridaRealizada(date("Y-m-d", $corte), date("H:i", $corte));
$tiposCorrida = array(
	"CORRIDA"      => "Corrida",
	"TREINO"       => "Treino",
	"TOMADA_TEMPO" => "Tomada de tempo"
);
$historico = $crud->listCorridaCliente($_SESSION['id']);
$agora = time();
$proximas  = array();
$anteriores = array();
foreach ($historico as $linha) {
	$quando = strtotime($linha['cor_data'] . " " . $linha['cor_horario']);
	if ($linha['cor_status'] == 'AGENDADA' && $quando > $agora) {
		$proximas[] = $linha;
	} else {
		$anteriores[] = $linha;
	}
}
$proximas = array_reverse($proximas);
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
					<?php if (count($historico) == 0) { ?>
						<p>Você ainda não participou de nenhuma corrida. Marque a sua primeira pelo calendário, no menu.</p>
					<?php } else { ?>
						<p><?php echo count($historico); ?> corrida(s) no total</p>
						<?php if (count($proximas) > 0) { ?>
							<h3>Próximas corridas</h3>
							<div id="container-tabela">
								<table class="tabela">
									<tr>
										<th>Data</th>
										<th>Horário</th>
										<th>Evento</th>
										<th>Acesso</th>
										<th>Pilotos</th>
										<th>Situação</th>
									</tr>
									<?php foreach ($proximas as $corrida) { ?>
										<tr>
											<td><?php echo date("d/m/Y", strtotime($corrida['cor_data'])); ?></td>
											<td><?php echo substr($corrida['cor_horario'], 0, 5); ?></td>
											<td><?php echo htmlspecialchars(isset($tiposCorrida[$corrida['cor_tipo']]) ? $tiposCorrida[$corrida['cor_tipo']] : $corrida['cor_tipo']); ?></td>
											<td>
												<?php
													if ($corrida['cor_visibilidade'] == 'PUBLICA') {
														echo "Pública";
													} else if ($corrida['fk_id_cli'] == $_SESSION['id']) {
														echo "Privada (sua)";
													} else {
														echo "Privada (convidado)";
													}
												?>
											</td>
											<td><?php echo (int) $corrida['total_part'] . " de " . (int) $corrida['cor_limite_pilot']; ?></td>
											<td>Agendada</td>
										</tr>
									<?php } ?>
								</table>
							</div>
						<?php } ?>
						<?php if (count($anteriores) > 0) { ?>
							<h3>Corridas anteriores</h3>
							<div id="container-tabela">
								<table class="tabela">
									<tr>
										<th>Data</th>
										<th>Horário</th>
										<th>Evento</th>
										<th>Acesso</th>
										<th>Pilotos</th>
										<th>Situação</th>
									</tr>
									<?php foreach ($anteriores as $corrida) { ?>
										<tr>
											<td><?php echo date("d/m/Y", strtotime($corrida['cor_data'])); ?></td>
											<td><?php echo substr($corrida['cor_horario'], 0, 5); ?></td>
											<td><?php echo htmlspecialchars(isset($tiposCorrida[$corrida['cor_tipo']]) ? $tiposCorrida[$corrida['cor_tipo']] : $corrida['cor_tipo']); ?></td>
											<td>
												<?php
													if ($corrida['cor_visibilidade'] == 'PUBLICA') {
														echo "Pública";
													} else if ($corrida['fk_id_cli'] == $_SESSION['id']) {
														echo "Privada (sua)";
													} else {
														echo "Privada (convidado)";
													}
												?>
											</td>
											<td><?php echo (int) $corrida['total_part'] . " de " . (int) $corrida['cor_limite_pilot']; ?></td>
											<td>
												<?php
													if ($corrida['cor_status'] == 'CANCELADA') {
														$quem = ($corrida["canc_cli"] == $_SESSION["id"]) ? "por você" : "pelo kartódromo";
														echo "Cancelada (" . $quem . ")";
														if ($corrida['canc_just'] != "") {
															echo "<br>" . htmlspecialchars($corrida['canc_just']);
														}
													} else if ($corrida['cor_status'] == 'REALIZADA') {
														echo "Realizada";
													} else {
														echo "Agendada";
													}
												?>
											</td>
										</tr>
									<?php } ?>
								</table>
							</div>
						<?php } ?>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
