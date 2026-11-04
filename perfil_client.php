<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/style_Perfil.css">
	<title>Perfil</title>
</head>
<?php

include_once("./db/config.php");
include_once("./db/setDb.php");
include_once("./crud.php");

if (!isset($_SESSION["id"])) {
	header("Location: login.php");
	exit();
}
?>

<body>



	<!--              Desktop HTML                 -->
	<div id="containerPc">

		<header>
			<nav>
				<a href="mainMenu.php"><button class="botoes">
						<p>Menu</p>
					</button></a>
				<a href="logout.php"><button class="botoes">
						<p>Sair</p>
					</button></a>
			</nav>
		</header>

		<div id="content">
			<div id="container-perfil">
				<div id="container-titulo">
					<h1>Seu perfil</h1>
				</div>
				<div id="container-dados">
					<p>ID:
						<?php echo  $_SESSION['id'] ?>
					</p>
					<p>Nome:
						<?php echo $_SESSION['login'] ?>
					</p>
					<p>Admin:
						<?php echo $_SESSION['admin'] ?>
					</p>
				</div>
			</div>

		</div>
	</div>

	<!---              Mobile HTML-                -->


	<div id="containerMobile">

		<header>
			<nav>
				<a href="mainMenu.php"><button class="botoes">
						<p>Menu</p>
					</button></a>
				<a href="logout.php"><button class="botoes">
						<p>Sair</p>
					</button></a>
			</nav>
		</header>

		<div id="content">
			<div id="container-perfil">
				<div id="container-titulo">
					<h1>Seu perfil</h1>
				</div>
				<div id="container-dados">
					<p>ID:
						<?php echo  $_SESSION['id'] ?>
					</p>
					<p>Nome:
						<?php echo $_SESSION['login'] ?>
					</p>
					<p>Admin:
						<?php echo $_SESSION['admin'] ?>
					</p>
				</div>
			</div>

		</div>

</body>

</html>