<?php
include_once("./db/config.php");
include_once("./db/setDb.php");
include_once("./crud.php");
require_once 'auth_emplo.php';

$crud = new Crud($db);
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
		<header>
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
				<div id="container-dados">
					<p>ID:
						<?php echo htmlspecialchars($funcionario['id_func']); ?>
					</p>
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
			</div>
		</div>
	</div>
</body>
</html>