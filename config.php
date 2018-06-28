<?php
session_start();
require_once __DIR__ . '/conexao.php';

// Definições
define('APP_PATH', 'http://localhost/site');
define('VALOR_CERVEJA', 9);
define('VALOR_REFRIAGUA', 4);
define('PORCENTAGEM_DESCONTO', 25);

// Ajustar a saída do valor
function price($val) {
  return number_format((float)$val, 2, ',', '');
}