<?php
require_once 'config.php';

if(!isset($_SESSION['logado'])) {
  header('location: entrar.php');
  exit;
}
$matricula = $_SESSION['logado'];
/****************************************************
 *   Exibe todos os dados do BD em um arquivo PDF   *
 ****************************************************/
ini_set('display_errors',1); // enable php error display for easy trouble shooting
error_reporting(E_ALL); // set error display to all

// Inicia a conversão
use Mpdf\Mpdf;

// Autoload do composer
require_once __DIR__ . '/vendor/autoload.php';

// Instancia
$mpdf = new Mpdf([
  'mode' => 'utf-8'
]);

$mpdf->SetHeader("Copa - CTG Eduardo Müller, Matricula: $matricula (Página {PAGENO})");  // optional - just as an example
$mpdf->CSSselectMedia = 'print';

$mpdf->SetTitle('Copa - CTG Eduardo Müller | Dados da matricula: ' . $matricula);


// Pega os dados do funcionario
$stmt = $PDO->prepare("SELECT id, nome, idade, matricula, cerveja, refriagua FROM funcionario WHERE matricula=?");
if(!$stmt->execute(array($matricula))) {
  unset($_SESSION['logado']);
  header("HTTP/1.0 404 Not Found");
  exit;
}
$usrData = $stmt->fetch();

// HTML a ser carregado
ob_start(); ?>

<!DOCTYPE html>
<html>
<head>
<style>
* {
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  -webkit-box-sizing: border-box;
}
html {
  padding: 2em;
  width: 210mm;
  margin: 0 auto;
}
body {
  font: 12pt Arial, Georgia, "Times New Roman", Times, serif;
}
table {
  width: 100%;
}
caption {
  text-align: left;
  padding: 16px 0;
}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
td {
  word-wrap: break-word;         /* All browsers since IE 5.5+ */
  overflow-wrap: break-word;     /* Renamed property in CSS3 draft spec */
}
th, td {
  padding: 8px 12px;
  text-align: left;
  line-height: 1.1;
}
.header {
  padding-top: 20px;
}
.header .title,
.header .subtitle {
  margin-bottom: 0;
  margin-top: 0;
}
.text-danger {
  color: #F44336;
}
.text-muted {
  color: #333;
}
.table {
  margin-top: 36px;
}
.text-right {
  text-align: right;
}
</style>
</head>
<body>
<div class="header">
	<h3 class="title">Total de Fichas</h3>
  <h5 class="subtitle">Nome: <strong><?= $usrData['nome'] ?></strong></h5>
  <h5 class="subtitle">Idade: <strong><?= $usrData['idade'] ?> anos</strong></h5>
	<h5 class="subtitle">Matricula Nº: <strong><?= $usrData['matricula']; ?></strong></h5>

	<hr/>
</div>

<div class="table">
  <!-- Cerveja -->
  <h5>Fichas de Cerveja</h5>

  <?php
  $fichas = $usrData['cerveja'];
  $total = $fichas * VALOR_CERVEJA;
  $desconto = $total * (PORCENTAGEM_DESCONTO / 100);
  $totalpag = $total - $desconto;
  ?>
  <table>
    <thead>
      <tr>
        <th>Total de Fichas</th>
        <th>Valor Unitário</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?= $fichas ?></td>
        <td>R$ <?= price(VALOR_CERVEJA); ?></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td class="text-right">Total</td>
        <td>R$ <?= price($total); ?></td>
      </tr>
      <tr>
        <td class="text-right">Desconto</td>
        <td class="text-danger">R$ -<?= price($desconto); ?> <span class="text-muted">(<?= PORCENTAGEM_DESCONTO ?>%)</span></td>
      </tr>
      <tr>
        <td class="text-right">Total a Pagar</td>
        <td><strong>R$ <?= price($totalpag); ?></strong></td>
      </tr>
    </tfoot>
  </table>

</div>
<div class="table">
  <!-- Refri/Água -->
  <h5>Fichas de Refri/Água</h5>
  
  <?php
  $fichas = $usrData['refriagua'];
  $total = $fichas * VALOR_REFRIAGUA;
  $desconto = $total * (PORCENTAGEM_DESCONTO / 100);
  $totalpag = $total - $desconto;
  ?>
  <table>
    <thead>
      <tr>
        <th>Total de Fichas</th>
        <th>Valor Unitário</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?= $fichas ?></td>
        <td>R$ <?= price(VALOR_REFRIAGUA); ?></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td class="text-right">Total</td>
        <td>R$ <?= price($total); ?></td>
      </tr>
      <tr>
        <td class="text-right">Desconto</td>
        <td class="text-danger">R$ -<?= price($desconto); ?> <span class="text-muted">(<?= PORCENTAGEM_DESCONTO ?>%)</span></td>
      </tr>
      <tr>
        <td class="text-right">Total a Pagar</td>
        <td><strong>R$ <?= price($totalpag); ?></strong></td>
      </tr>
    </tfoot>
  </table>
</div>
</body>
</html>

<?php
$html = ob_get_clean();
$mpdf->WriteHTML($html);

// Exibir e salvar o arquivo
$mpdf->Output();
//$mpdf->Output($matricula.'.pdf', \Mpdf\Output\Destination::FILE);