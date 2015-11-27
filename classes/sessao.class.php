<?php 
require_once(dirname(__FILE__).'/autoload.php');
protegeArquivo(basename(__FILE__));
class sessao{
	protected $id; //Armazena o Id da sessão
	protected $nvars; //Numero de variáveis ou número de campos qua a sessão tem.

	public function __construct($inicia=true){
		if($inicia == TRUE):
			$this->start(); //Quando constroe a classe inicia a sessão, chama a função start.
		endif;
	}

	public function start(){
		session_start(); //Dá um start na sessão. Inicia uma sessão
		$this->id = session_id(); //Seta a propriedade id com o id da sessão.
		$this->setNvars(); //Seta o número de variáveis com a função setNvars.
	}

	public function setNvars(){
		$this->nvars = sizeof($_SESSION); //Seta a propriedade nvars com o tamanho da nossa sessão.
	}

	public function getNvars(){
		return $this->nvars; //Retorna o número de variáveis
	}

	public function setVar($var, $valor){ //Seta / define o valor para uma variável, 
		$_SESSION[$var] = $valor; //exemplo variável nome recebe o valor 'fulano'
		$this->setNvars(); //Atualiza o número de campos da nossa variável.
	}

	public function unsetVar($var){ 
		unset($_SESSION[$var]); //Exclui o valor de um campo
		$this->setNvars; //Atualiza o número de campos da nossa variável.
	}

	public function getVar($var){ //Retorna o valor de uma determinada variável da sessão
		if(isset($_SESSION[$var])): // Se existir
			return $_SESSION[$var];
		else:
			return NULL; //Caso não exista retorna null.
		endif;
	}

	public function destroy($inicia=false){ // Destroi uma sessão
		session_unset(); //Tira todos os campos armazenados na sessão
		session_destroy();
		$this->setNvars(); //Atualiza o número de varíáveis.
		if($inicia==TRUE): //Se inicia for true, inicia automaticamente uma nova sessão.
			$this->start();
		endif;
	}

	public function printAll(){ //Somente para testes.
		foreach ($_SESSION as $key => $value) :
			printf("%s = %s<br />", $key, $value);
		endforeach;
	}
}
?>