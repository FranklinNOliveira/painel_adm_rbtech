<?php  include('header.php'); 
if(isset($_GET['m'])) $modulo = $_GET['m'];
if(isset($_GET['t'])) $tela = $_GET['t'];
?>
<div id="content">
	<?php 
	if($modulo && $tela):
		loadmodulo($modulo,$tela);
	else:
		echo '<p>Escolha uma opção de menu ao lado</p>';
	endif;
	?>
	<p>Conteúdo do Painel</p>
</div> <!-- content -->
<?php  include('sidebar.php'); ?>
<?php  include("footer.php"); ?>
