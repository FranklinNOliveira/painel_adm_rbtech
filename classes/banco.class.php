<?php
	require_once(dirname(__FILE__).'/autoload.php');
	protegeArquivo(basename(__FILE__));
	abstract class banco{
		// Propriedades
		public $servidor		= DBHOST;	//
		public $usuario			= DBUSER;	//
		public $senha			= DBPASS;	//
		public $nomebanco		= DBNAME;	//
		public $conexao			= NULL;		// Armazena a conexao com o banco de dados
		public $dataset			= NULL;		// Armazena os resultados das nossas pesquisas
		public $linhasafetadas	= -1;		// Armazena a quantidade de linhas que uma pesquisa afetar no banco de dados
		

		public function __construct(){
			$this->conecta();
		} // Fim método construtor
		
		public function __destruct(){
			if($this->conexao != NULL):
				mysqli_close($this->conexao);
			endif;
		} // Fim método desconexao
		
		//Métodos
		public function conecta(){
			$this ->conexao = @mysqli_connect($this->servidor, $this->usuario, $this->senha, $this->nomebanco) 
			or die ($this->trataerro(__FILE__, __FUNCTION__, mysqli_connect_errno($this->conexao), mysqli_connect_error($this->conexao), TRUE));
			@mysqli_set_charset($this->conexao, 'utf8') or die ($this->trataerro(__FILE__, __FUNCTION__, mysqli_connect_errno($this->conexao), mysqli_connect_error($this->conexao), TRUE));
		} // Fim conecta
		
		
		// Inserir dados no banco de dados.
		public function inserir($objeto){
			// INSERT INTO tabela (campo 1, campo2) VALUES (valor 1, valor2)
			$sql = "INSERT INTO ".$objeto->tabela." (";
				for($i = 0; $i < count($objeto->campos_valores); $i++):
					$sql .= key($objeto->campos_valores);
					if($i < (count($objeto->campos_valores)-1)):
						$sql .=", ";
					else:
						$sql .=") ";
					endif;
					next($objeto->campos_valores);
				endfor;
				
				reset($objeto->campos_valores);
				$sql .= "VALUES (";
				for($i = 0; $i < count($objeto->campos_valores); $i++):
					$sql .= is_numeric($objeto->campos_valores[key($objeto->campos_valores)]) ? 
						$objeto->campos_valores[key($objeto->campos_valores)] : 
						"'".$objeto->campos_valores[key($objeto->campos_valores)]."'";
					if($i < (count($objeto->campos_valores)-1)):
						$sql .=", ";
					else:
						$sql .=") ";
					endif;
					next($objeto->campos_valores);
				endfor;
			return $this->executaSQL($sql);	
		} //Fim classe inserir
		
		//Classe atualizar dados bando de dados
		public function atualizar($objeto){
			// UPDATE tabela SET campo1 = valor1, campo2 = valor2 WHERE campoChave = valorChave
			$sql = "UPDATE ".$objeto->tabela." SET ";
				for($i = 0; $i < count($objeto->campos_valores); $i++):
					$sql .= key($objeto->campos_valores)."=";
					$sql .= is_numeric($objeto->campos_valores[key($objeto->campos_valores)]) ? 
						$objeto->campos_valores[key($objeto->campos_valores)] : 
						"'".$objeto->campos_valores[key($objeto->campos_valores)]."'";
					if($i < (count($objeto->campos_valores)-1)):
						$sql .=", ";
					else:
						$sql .=" ";
					endif;
					next($objeto->campos_valores);
				endfor;
				$sql .="WHERE ".$objeto->campopk."=";
				$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'";
				echo $sql;
			return $this->executaSQL($sql);		
		} // Fim classe atualizar
		
		// Classe para deletar dados do banco de dados
		public function deletar($objeto){
			// DELETE FROM tabela WHERE campoChave = valorChave
				$sql = "DELETE FROM ".$objeto->tabela;
				$sql .=" WHERE ".$objeto->campopk."=";
				$sql .= is_numeric($objeto->valorpk) ? $objeto->valorpk : "'".$objeto->valorpk."'";
				echo $sql;
				return $this->executaSQL($sql);
		} // fim classe deletar
		
		// Classe para selecção de TODOS os dados no bando de dados
		public function selecionaTudo($objeto){
			// 
			$sql = "SELECT * FROM ".$objeto->tabela;
			if($objeto->extras_select !=NULL):
				$sql .= " ".$objeto->extras_select;
			endif;
			return $this->executaSQL($sql);
		} // Fim classe seleciona tudo
		
			// Classe para selecção de dados no bando de dados
		public function selecionaCampos($objeto){
			$sql = "SELECT ";
			for($i = 0; $i < count($objeto->campos_valores); $i++):
					$sql .= key($objeto->campos_valores);
					if($i < (count($objeto->campos_valores)-1)):
						$sql .=", ";
					else:
						$sql .=" ";
					endif;
					next($objeto->campos_valores);
				endfor;
			
			$sql .= " FROM ".$objeto->tabela;
			if($objeto->extras_select !=NULL):
				$sql .= " ".$objeto->extras_select;
			endif;
			return $this->executaSQL($sql);
		} // Fim classe seleciona tudo
		
		public function executaSQL($sql=NULL){
			if($sql != NULL):
				$query = mysqli_query($this->conexao,$sql) or ($this->trataerro(__FILE__,__FUNCTION__));
				$this->linhasafetadas = mysqli_affected_rows($this->conexao);
				if(substr(trim(strtolower($sql)),0,6)=='select'):
					$this->dataset = $query;
					return $query;
				else:
					return $this->linhasafetadas;
				endif;
			else:
				$this->trataerro(__FILE__, __FUNCTION__, NULL,'Comando SQL não informado na rotina',FALSE);
			endif;	
		} //Fim classe executa SQL 
		
		public function retornaDados($tipo=NULL){
			switch (strtolower($tipo)):
				case "array":
					return mysqli_fetch_array($this->dataset);
					break;
				case "assoc":
					return mysqli_fetch_assoc($this->dataset);
					break;
				case "object":
					return mysqli_fetch_object($this->dataset);
					break;
				default:
					return mysqli_fetch_object($this->dataset);
				endswitch;
		} // Fim classe retorna dados
		
		
		public function trataerro($arquivo=NULL, $rotina=NULL, $numerro=NULL, $msgerro=NULL, $geraexcept=FALSE){
			if ($arquivo==NULL) $arquivo="Não informado";
			if ($rotina==NULL) $rotina="Não informada";
			if ($numerro==Null)$numerro=mysqli_connect_errno($this->conexao);
			if ($msgerro==NULL) $msgerro=mysqli_connect_error($this->conexao);
			$resultado = 'Ocorreu um erro com os seguintes detalhes:</br>
				<strong>Arquivo: </strong>'.$arquivo.'</br>
				<strong>Rotina: </strong>'.$rotina.'</br>
				<strong>Código: </strong>'.$numerro.'</br>
				<strong>Mensagem: </strong>'.$msgerro;
				
			if ($geraexcept==FALSE):
				echo($resultado);
			else :
				die($resultado);
			endif;
		} // Fim rotina de tratamento de erro.
		
		
		
	} //Fim classe banco
?>