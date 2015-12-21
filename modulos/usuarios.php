<?php 
require_once(dirname(dirname(__FILE__))."/funcoes.php");
protegeArquivo(basename(__FILE__));
loadJS('jquery-validate');
loadJS('jquery-validate-messages');
switch ($tela):
	case 'login': 
		$sessao = new sessao();
		if($sessao->getNvars()>0 && $sessao->getVar('logado')==TRUE && $sessao->getVar('ip') == $_SERVER['REMOTE_ADDR']) redireciona('painel.php');
		if (isset($_POST['logar'])):
			$user = new usuarios();
			$user->setValor('login', antiInject($_POST['usuario']));
			$user->setValor('senha', antiInject($_POST['senha']));
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
							<input type="text" size="35" name="usuario" autofocus="autofocus" value="<?php echo $_POST['usuario']; ?>">
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
		//Validação dos dados
		if(isset($_POST['cadastrar'])):
			$user = new usuarios(array(
				'nome'=>$_POST['nome'],
				'email'=>$_POST['email'],
				'login'=>$_POST['login'],
				'senha'=>codificaSenha($_POST['senha']),
				'administrador'=>($_POST['adm']=='on') ? 's' : 'n',
			));

			if($user->existeRegistro('login',$_POST['login'])):
				printMsg('Este login já está cadastrado, escolha outro nome de usuário.','erro');
				$duplicado = TRUE;
			endif;
			if($user->existeRegistro('email',$_POST['email'])):
				printMsg('Este email já está cadastrado, escolha outro endereço.', 'erro');
				$duplicado = TRUE;
			endif;
			if($duplicado!=TRUE):
				$user->inserir($user);
				if($user->linhasafetadas==1):
					printMsg('Dados inseridos com sucesso. <a href="'.ADMURL.'?m=usuarios&t=listar">Exibir cadastros</a>') ;
					unset($_POST);
				endif;
			endif;
		endif;
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
					<input type="text" size="50" name="nome" autofocus="autofocus" value="<?php echo $_POST['nome'] ?>" ></li>

					<li><label for="email">Email:</label>
					<input type="text" size="50" name="email" value="<?php echo $_POST['email'] ?>" ></li>

					<li><label for="login">Login:</label>
					<input type="text" size="35" name="login" value="<?php echo $_POST['login'] ?>" ></li>

					<li><label for="senha">Senha:</label>
					<input type="password" size="25" name="senha" id="senha" value="<?php echo $_POST['senha'] ?>" ></li>

					<li><label for="senhaconf">Repita a senha:</label>
					<input type="password" size="25" name="senhaconf" value="<?php echo $_POST['senhaconf'] ?>" ></li>

					<li><label for="adm">Administrador:</label>
					<input type="checkbox" name="adm" <?php if(!isAdmin()) echo 'disabled="disabled"'; if($_POST['adm']) echo 
'checked="checked"'; ?> /> dar controle total ao usuário</li>
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
		loadCSS('data-table', NULL, TRUE);
		loadJS('jquery-datatable');

		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$("#listausers").dataTable({
			        "language": {
		            "zeroRecords": "Nenhum dado para exibição",
		            "info": "Mostrando _START_ a _END_ de _TOTAL_ de registros",
		            "infoEmpty": "Nenhum registro para ser exibido",
		            "infoFiltered": "(filtrado de _MAX_ registros no total)",
		            "search": "Pesquisar",
					},
					"sScrollY" : "450px",
					"bPaginate" : false,
					"aaSorting": [[0, "asc"]]
				});
			});
		</script>
		<table cellspacing="0" cellspading="0" class="display" id="listausers">
			<thead>
				<tr>
					<th>Nome</th><th>Email</th><th>Login</th><th>Ativo/Adm</th><th>Cadastro</th><th>Ações</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$user = new usuarios();
				$user->selecionaTudo($user);
				while ($res = $user->retornaDados()){
					echo '<tr>';
					printf('<td>%s</td>',$res->nome);
					printf('<td>%s</td>',$res->email);
					printf('<td>%s</td>',$res->login);
					printf('<td class="center">%s/%s</td>',strtoupper($res->ativo),strtoupper($res->administrador));
					printf('<td class="center">%s</td>',date("d/m/y",strtotime($res->datacad)));
					printf('<td class="center">
							<a href="?m=usuarios&t=incluir" title="Novo cadastro"><img src="images/add.png" alt="Novo cadastro" /></a>
							<a href="?m=usuarios&t=editar&id=%s" title="Editar"><img src="images/edit.png" alt="Editar" /></a>
							<a href="?m=usuarios&t=senha&id=%s" title="Alterar Senha"><img src="images/pass.png" alt="Alterar senha" /></a>
							<a href="?m=usuarios&t=excluir&id=%s" title="Excluir cadastro"><img src="images/delete.png" alt="Excluir cadastro" /></a>
						</td>',$res->id,$res->id,$res->id);
					echo '</tr>';
				};
				?>
			</tbody>
		</table>




		<?php
	break;
	case 'editar':
		echo '<h2>Edição de usuários</h2>';
		$sessao = new sessao();
		if (isAdmin()==TRUE || $sessao->getVar('iduser')==$_GET['id']):
			// Permissão para alerar.
			$id = $_GET['id'];
			if (isset($_GET['id'])) :
				//Faz a edição do user
				if (isset($_POST['editar'])):
					if(isAdmin() == TRUE):
						$user = new usuarios(array(
							'nome' => $_POST['nome'],
							'email' => $_POST['email'],
							'ativo' => $_POST['ativo']=='on' ? 's' : 'n',
							'administrador' => $_POST['adm']=='on' ? 's' : 'n',
						));
					else:
						$user = new usuarios(array(
							'nome' => $_POST['nome'],
							'email' => $_POST['email'],
						));
					endif;
					$user->valorpk = $id;
					$user->extras_select = "WHERE id=$id";
					$user->selecionaTudo($user);
					$res = $user->retornaDados();
					if ($res->email != $_POST['email']) :
						if ($user->existeRegistro('email',$_POST['email'])):
							printMsg('Este email já existe no sistema, escolha outro endereço!','erro');
							$duplicado = TRUE;
						endif;
					endif;
					if ($duplicado != TRUE) :
						$user->atualizar($user);
						if ($user->linhasafetadas==1) :
							printMsg('Dados alterados com sucesso. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');
						else:
							printMsg('Nenhum dado foi alterado. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');

						endif;
					endif;
				endif;
				$userbd = new usuarios();
				$userbd->extras_select = "WHERE id=$id";
				$userbd->selecionaTudo($userbd);
				$resbd = $userbd->retornaDados();
			else:
				//Avisa para selecionar o user
				printMsg('Usuário não definido, <a href="?m=usuarios&t=listar">escolha um usuário para alterar</a>','erro');
			endif;
			?>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".userform").validate({
					rules:{
						nome:{required:true, minlength:3},
						email:{required:true, email:true},
					}
				});
			});
		</script>
		<form class="userform" method="post" action="">
			<fieldset>
				<legend>Informe os dados para alteração</legend>
				<ul>
					<li><label for="nome">Nome:</label>
					<input type="text" size="50" name="nome" autofocus="autofocus" value="<?php if($resbd) echo $resbd->nome; ?>" ></li>

					<li><label for="email">Email:</label>
					<input type="text" size="50" name="email" value="<?php if($resbd) echo $resbd->email; ?>" ></li>

					<li><label for="login">Login:</label>
					<input type="text" disabled="disabled" size="35" name="login" value="<?php if($resbd) echo $resbd->login; ?>" ></li>

					<li><label for="adm">Ativo:</label>
					<input type="checkbox" name="ativo" <?php if(!isAdmin()) echo 'disabled="disabled"'; if($resbd->administrador == 's') echo 
'checked="checked"'; ?> /> habilitar ou desabilitar o usuário</li>

					<li><label for="adm">Administrador:</label>
					<input type="checkbox" name="adm" <?php if(!isAdmin()) echo 'disabled="disabled"'; if($resbd->administrador == 's') echo 
'checked="checked"'; ?> /> dar controle total ao usuário</li>
					<li class="center">
						<input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar">
						<input type="submit" name="editar" value="Salvar alterações">
					</li>
				</ul>	
			</fieldset>
		</form>


			<?php
		else:
			//Avisa que não tem permissão para alterar.
			printMsg('Você não tem permissão para acessar esta página. <a href="#" onclick="history.back()">Voltar</a>','erro');
		endif;
	break;
	case 'senha':
		echo '<h2>Alteração de senha</h2>';
		$sessao = new sessao();
		if (isAdmin()==TRUE || $sessao->getVar('iduser')==$_GET['id']):
			// Permissão para alerar.
			$id = $_GET['id'];
			if (isset($_GET['id'])) :
				//Faz a edição do user
				if (isset($_POST['mudasenha'])):
					$user = new usuarios(array(
						'senha' => codificaSenha($_POST['senha']),
					));
					$user->valorpk = $id;
					$user->atualizar($user);
					if ($user->linhasafetadas==1) :
						printMsg('Senha alterada com sucesso. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');
					else:
						printMsg('Nenhum dado foi alterado. <a href="?m=usuarios&t=listar">Exibir cadastros</a>','alerta');
					endif;
				endif;
				$userbd = new usuarios();
				$userbd->extras_select = "WHERE id=$id";
				$userbd->selecionaTudo($userbd);
				$resbd = $userbd->retornaDados();
			else:
				//Avisa para selecionar o user
				printMsg('Usuário não definido, <a href="?m=usuarios&t=listar">escolha um usuário para alterar</a>','erro');
			endif;
			?>
		<script type="text/javascript">
			$(document).ready(function(){
				$(".userform").validate({
					rules:{
						senha:{required:true, rangelength:[4,10]},
						senhaconf:{required:true, equalTo:"#senha"},
					}
				});
			});
		</script>
		<form class="userform" method="post" action="">
			<fieldset>
				<legend>Informe os dados para alteração</legend>
				<ul>
					<li><label for="nome">Nome:</label>
					<input type="text" disabled="disabled" size="50" name="nome" value="<?php if($resbd) echo $resbd->nome; ?>" ></li>

					<li><label for="email">Email:</label>
					<input type="text" disabled="disabled" size="50" name="email" value="<?php if($resbd) echo $resbd->email; ?>" ></li>

					<li><label for="login">Login:</label>
					<input type="text" disabled="disabled" size="35" name="login" value="<?php if($resbd) echo $resbd->login; ?>" ></li>

					<li><label for="senha">Senha:</label>
					<input type="password" size="25" name="senha" autofocus="autofocus" id="senha" value="<?php echo $_POST['senha'] ?>" ></li>

					<li><label for="senhaconf">Repita a senha:</label>
					<input type="password" size="25" name="senhaconf" value="<?php echo $_POST['senhaconf'] ?>" ></li>
					
					<li class="center">
						<input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar">
						<input type="submit" name="mudasenha" value="Salvar alterações">
					</li>
				</ul>	
			</fieldset>
		</form>


			<?php
		else:
			//Avisa que não tem permissão para alterar.
			printMsg('Você não tem permissão para acessar esta página. <a href="#" onclick="history.back()">Voltar</a>','erro');
		endif;
	break;
	break;
	case 'excluir':
		echo '<h2>Exclusão de usuários</h2>';
		$sessao = new sessao();
		if (isAdmin()==TRUE):
			if (isset($_GET['id'])) :
				$id = $_GET['id'];
				//Faz a exclusão do user
				if (isset($_POST['excluir'])):
					$user = new usuarios();
					$user->valorpk = $id;
					$user->deletar($user);
					if ($user->linhasafetadas==1) :
						printMsg('Registro excluido com sucesso. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');
					else:
						printMsg('Nenhum registro foi excluido. <a href="?m=usuarios&t=listar">Exibir cadastros</a>');

					endif;
				endif;
				$userbd = new usuarios();
				$userbd->extras_select = "WHERE id=$id";
				$userbd->selecionaTudo($userbd);
				$resbd = $userbd->retornaDados();
			else:
				//Avisa para selecionar o user
				printMsg('Usuário não definido, <a href="?m=usuarios&t=listar">escolha um usuário para excluir</a>','erro');
			endif;
			?>
		<form class="userform" method="post" action="">
			<fieldset>
				<legend>Confira os dados para exclusão</legend>
				<ul>
					<li><label for="nome">Nome:</label>
					<input type="text" disabled="disabled" size="50" name="nome" value="<?php if($resbd) echo $resbd->nome; ?>" ></li>

					<li><label for="email">Email:</label>
					<input type="text" disabled="disabled" size="50" name="email" value="<?php if($resbd) echo $resbd->email; ?>" ></li>

					<li><label for="login">Login:</label>
					<input type="text" disabled="disabled" size="35" name="login" value="<?php if($resbd) echo $resbd->login; ?>" ></li>

					<li><label for="adm">Ativo:</label>
					<input type="checkbox" disabled="disabled" name="ativo" <?php if($resbd->administrador == 's') echo 
'checked="checked"'; ?> /> habilitar ou desabilitar o usuário</li>

					<li><label for="adm">Administrador:</label>
					<input type="checkbox" disabled="disabled" name="adm" <?php if($resbd->administrador == 's') echo 
'checked="checked"'; ?> /> dar controle total ao usuário</li>
					<li class="center">
						<input type="button" onclick="location.href='?m=usuarios&t=listar'" value="Cancelar">
						<input type="submit" name="excluir" value="Confirmar exclusão">
					</li>
				</ul>	
			</fieldset>
		</form>


			<?php
		else:
			//Avisa que não tem permissão para alterar.
			printMsg('Você não tem permissão para acessar esta página. <a href="#" onclick="history.back()">Voltar</a>','erro');
		endif;
	break;
	break;
	default:
		echo '<p>A tela solicitada não existe!</p>';
	break;
endswitch;
?>