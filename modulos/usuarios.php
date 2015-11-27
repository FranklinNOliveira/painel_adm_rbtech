<?php 
require_once(dirname(dirname(__FILE__))."/funcoes.php");
protegeArquivo(basename(__FILE__));
loadJS('jquery-validate');
loadJS('jquery-validate-messages');
switch ($tela):
	case 'login': 
		$sessao = new sessao();
		if($sessao->getNvars()>0 || $sessao->getVar('logado')==TRUE || $sessao->getVar('ip') == $_SERVER['REMOTE_ADDR']) redireciona('painel.php');
		if (isset($_POST['logar'])):
			$user = new usuarios();
			$user->setValor('login', $_POST['usuario']);
			$user->setValor('senha', $_POST['senha']);
			if ($user->doLogin($user)):
				redireciona('painel.php');
			else:
				redireciona('?erro=2');
			endif;
		endif;
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".userform").validate({
					rules:{
						usuario:{required:true, minlength:3},
						senha:{required:true, rangelength:[4,10]},
					}
				});
			});

		</script>
		<div id="loginform">
			<form class="userform" method="post" action="">
				<fieldset>
					<legend>Acesso restrito, identifique-se</legend>
					<ul>
						<li>
							<label for="usuario">Usuario:</label>
							<input type="text" size="35" name="usuario" value="<?php echo $_POST['usuario']; ?>">
						</li>
						<li>
							<label for="senha">Senha:</label>
							<input type="password" size="35" name="senha" value="<?php echo $_POST['senha'] ?>" >
						</li>
						<li class="center"><input class="radius5" type="submit" name="logar" value="Login"/></li>
					</ul>
					<?php 
					 $erro = $_GET['erro'];
					 switch ($erro) {
					 	case 1:
					 		echo '<div class="sucesso">Você fez logof no sistema.</div>';
					 		break;
					 	case 2:
					 		echo '<div class="erro">Dados incorretos ou usuário inativo.</div>';
					 		break;
					 	case 3:
					 		echo '<div class="erro">Faça Login antes de acessar a página solicitada.</div>';
					 		break;
					 }
					?>

				</fieldset>
			</form>	
		</div>	
		<?php
		break;
	case 'incluir':
		echo '<h2>Cadastro de usuários</h2>';
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".userform").validate({
					rules:{
						nome:{required:true, minlength:3},
						email:{required:true, email:true},
						login:{required:true, minlength:5},
						senha:{required:true, rangelength:[4,10]},
						senhaconf:{required:true, equalTo:"#senha"},
					}
				});
			});
		</script>
		<form class="userform" method="post" action="">
			<fieldset>
				<legend>Informe os dados para cadastro</legend>
				<ul>
					<li><label for="nome">Nome:</label>
					<input type="text" size="50" name:"nome" value="<?php echo $_POST['nome'] ?>" ></li>

					<li><label for="email">Email:</label>
					<input type="text" size="50" name:"email" value="<?php echo $_POST['email'] ?>" ></li>

					<li><label for="login">Login:</label>
					<input type="text" size="35" name:"login" value="<?php echo $_POST['login'] ?>" ></li>

					<li><label for="senha">Senha:</label>
					<input type="password" size="25" name:"senha" id="senha" value="<?php echo $_POST['senha'] ?>" ></li>

					<li><label for="senhaconf">Repita a senha:</label>
					<input type="password" size="25" name:"senhaconf" value="<?php echo $_POST['senhaconf'] ?>" ></li>

					<li><label for="adm">Administrador:</label>
					<input type="checkbox" name:"adm" /> dar controle total ao usuário</li>
					<li class="center">
						<input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar">
						<input type="submit" name="cadastrar" value="Salvar dados">
					</li>
				</ul>	
			</fieldset>
		</form>
		<?php
		break;
		case 'listar':
		echo '<h2>Usuários Cadastrados</h2>';
		break;
	default:
		echo '<p>A tela solicitada não existe!</p>';
		break;
endswitch;
?>