<?php
// Defne constantes
//diretório do sistema
	define("BASEPATH", dirname(__FILE__)."/"); //Define o diretório do sistema via localização
	define("BASEURL", "http://localhost/painel_adm_rbtech/"); //URL base do sistema (menu principal)
	define("ADMURL", BASEURL."painel.php"); //URL base do menu principal
	define("CLASSESPATH", "classes/"); // Diretório das classes
	define("MODULOSPATH", "modulos/"); // Diretório dos módulos
	define("CSSPATH", "css/"); // Diretório dos CSS
	define("JSPATH", "js/"); // Diretório dos arquivos JavaScript

	//Banco de dados
	define("DBHOST", "localhost"); // Host do banco de dados
	define("DBUSER", "root"); // usuário
	define("DBPASS", ""); // Senha
	define("DBNAME", "painel-adm-rbtech"); // Diretório base do banco de dados.

	
?>