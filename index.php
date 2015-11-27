<?php require_once("funcoes.php"); ?>
<!DOCTYPE html>
<html lang="pt_BR">
<head>
	<meta charset="UTF-8">
	<title>Painel Administrativo</title>
	<?php 
		loadCSS('reset');
		loadCSS('style');
		loadJS('jquery');
		loadJS('geral');

	?>
</head>
<body>
	<?php loadModulo('usuarios','login'); ?>
</body>
</html>