<?php
	require_once(dirname(__FILE__).'/autoload.php');
	protegeArquivo(basename(__FILE__));
	abstract class base extends banco{
		// Propriedades
		public $tabela = "";
		public $campos_valores = array(); // Armazena os campos e valores da tabela
		public $campopk = NULL; // Campo chave primária.
		public $valorpk = NULL; // Valor da chave primaria
		public $extras_select = ""; // Parametros para SQL de consulta.
		
		//Métodos
		public function addCampo($campo=NULL, $valor=NULL){ //Verifica o array e adiciona o campo
			if($campo != null):
				$this->campos_valores[$campo] = $valor;
			endif;
		} // Add Campo
		
		public function delCampo($campo=NULL){
			if(array_key_exists($campo, $this->campos_valores)):
				unset($this->campos_valores[$campo]);
			endif;
		} //Del Campo
		
		public function setValor($campo=NULL, $valor=NULL){ //Seta valor a um determinado campo.
			if($campo != null && $valor != null):
				$this->campos_valores[$campo] = $valor;
			endif;
		} // Set valor
		
		public function getValor($campo=NULL){ //Pega o valor de um deterinado campo
			if ($campo!=NULL && array_key_exists($campo, $this->campos_valores)):
				return $this->campos_valores[$campo];
			else:
				return FALSE;
			endif;
		} // Get Valor
	} //Fim classe base
?>