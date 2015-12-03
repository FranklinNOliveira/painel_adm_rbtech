<?php
	require_once(dirname(__FILE__).'/autoload.php');
	protegeArquivo(basename(__FILE__));
	class usuarios extends base{ //Responsável por manipular a tabela Clientes.
		public function __construct($campos=array()){
			
			parent::__construct(); //Chama o construtor da classe mãe.
			$this->tabela = "paineladm_usuarios";
			
			//Constroe um array com os campos da tabela usuários.
			if(sizeof($campos) <= 0):
				$this->campos_valores = array(
					'nome' => NULL,
					'email' => NULL,
					'login' => NULL,
					'senha' => NULL,
					'ativo' => NULL,
					'administrador' => NULL,
					'datacad' => NULL
				);
			else:
				$this->campos_valores = $campos;
			endif;
				$this->campopk = "id";
				
		} // Fim construct 
		public function doLogin($objeto){
			$objeto->extras_select = "WHERE login='".$objeto->getValor('login')."' AND senha='".codificaSenha($objeto->getValor('senha'))."' AND ativo='s'";
			$this->selecionaTudo($objeto);
			$sessao = new sessao();
			if($this->linhasafetadas==1):
				$uslogado = $objeto->retornaDados();
				$sessao->setVar('iduser', $uslogado->id);
				$sessao->setVar('nomeuser', $uslogado->nome);
				$sessao->setVar('loginuser', $uslogado->login);
				$sessao->setVar('logado', TRUE);
				$sessao->setVar('ip', $_SERVER['REMOTE_ADDR']);
				return TRUE;
			else:
				$sessao->destroy(TRUE);
				return FALSE;
			endif;
		}

		public function doLogout(){
			$sessao = new sessao();
			$sessao->destroy(TRUE);
			redireciona('?erro=1');
		}

		public function existeRegistro($campo=NULL, $valor=null){
			if ($campo!=null && $valor!=null):
				is_numeric($valor) ? $valor = $valor : $valor = "'".$valor."'";
				$this->extras_select = "WHERE $campo=$valor";
				$this->selecionaTudo($this);
				if($this->linhasafetadas > 0):
					return TRUE;
				else:
					return FALSE;
				endif;
			else:
				$this->trataerro(__FILE__,__FUNCTION__,NULL,'Faltam parâmetros para executar a função',TRUE);
			endif;
		}





	} // fim classe usuários
?>