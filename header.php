<?php require_once("funcoes.php"); 
protegeArquivo(basename(__FILE__));
verificaLogin();
$sessao = new sessao();
?>
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
<body class="painel">
	<div id="wrapper">
		<div id="header">
			<h1>Painel de Administração</h1>

		</div><!-- header -->
		<div id="wrap-content">
			

