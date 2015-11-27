<?php 
$pathlocal = dirname(__FILE__);
require_once(dirname($pathlocal)."/funcoes.php");
function __autoload($classe){
	$classe = str_replace('..', '', $classe);
	require_once($pathlocal."/$classe.class.php");
}
?>