<?php

$host = "localhost";
$usuario = "root";
$senha = "";
$bd = "cadastro1";

try {
	$PDO = new PDO("mysql:host=".$host.";dbname=".$bd, $usuario, $senha);
	$PDO->exec("set names utf8");
} catch(PDOException $e) {
	die('Falha na conexão: ' . $e->getmessage());
}

?>