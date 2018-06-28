<?php 
require_once 'config.php';

// Verifica se não está logado
if(!isset($_SESSION['logado'])) {
  header('location: entrar.php');
  exit;
}

// Logout
if(isset($_GET['logout'])) {
  unset($_SESSION['logado']);
  header('location: entrar.php?logout=1');
  exit;
}

// Pega a matricula do funcionario conectado
$matricula = $_SESSION['logado'];

// Atualizar dados
if(isset($_POST['update']) && 
  (isset($_POST['update']['cerveja']) || isset($_POST['update']['refriagua'])) &&
  isset($_POST['update']['action'])) {
  $form = $_POST['update'];
  $name = isset($form['cerveja']) ? 'cerveja' : 'refriagua';
  $val = isset($form['cerveja']) ? $form['cerveja'] : $form['refriagua'];
  $operador = $form['action'] == 'add' ? '+' : '-';

  $stmt = $PDO->prepare("UPDATE funcionario SET $name=GREATEST($name $operador ?, 0) WHERE matricula=?");
  if($stmt->execute(array($val, $matricula))) {
    header('location: index.php?atualizado=1');
    exit;
  } else {
    header('location: index.php?atualizado=0');
    exit;
  }
}

// Limpar dados
if(isset($_POST['limpafichas'])) {
  $name = $_POST['limpafichas'] == 'cerveja' ? 'cerveja' : 'refriagua';
  $stmt = $PDO->prepare("UPDATE funcionario SET $name=0 WHERE matricula=?");
  if($stmt->execute(array($matricula))) {
    header('location: index.php?limpo=1');
  } else {
    header('location: index.php?limpo=0');
  }
}

// Pega os dados do funcionario
$stmt = $PDO->prepare("SELECT id, nome, idade, matricula, cerveja, refriagua FROM funcionario WHERE matricula=?");
if(!$stmt->execute(array($matricula))) {
  unset($_SESSION['logado']);
  header('location: entrar.php?usr_error=1');
  exit;
}
$usrData = $stmt->fetch();

// Mensagens
if(isset($_GET['atualizado'])) {
  $sucesso = $_GET['atualizado'];
  if($sucesso) $info = "Dados atualizados com sucesso.";
  else $error = "Erro ao atualizar os dados.";
}
if(isset($_GET['limpo'])) {
  $sucesso = $_GET['limpo'];
  if($sucesso) $info = "Dados limpos com sucesso.";
  else $error = "Erro ao limpar os dados.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Copa - CTG Eduardo Muller | Compras</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom fonts for this template -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:200,200i,300,300i,400,400i,600,600i,700,700i,900,900i" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Merriweather:300,300i,400,400i,700,700i,900,900i" rel="stylesheet">
  <link href="vendor/fortawesome/font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet">

</head>
<body>

  <div class="jumbotron px-5">
    <h2>Fichas acumuladas:</h2> 
    <p class="text-muted">Informamos aqui o valor total a pagar das suas fichas utilizadas.</p>
  </div>

  <div class="container py-4">
    <div class="row mb-5">
      <div class="col-sm-12 col-md">
        <h4><?= $usrData['nome']; ?></h4>
        <h6 class="text-muted"><?= $usrData['matricula'] ?></h6>
      </div>
      <div class="col-sm-12 col-md-auto">
        <a href="<?= $_SERVER['PHP_SELF'] ?>?logout=1" class="btn btn-danger">Desconectar</a>
        <a href="pdf.php" class="btn btn-outline-dark"><i class="fa fa-file-pdf-o mr-1"></i> Gerar PDF</a>
      </div>
    </div>

    <?php if(isset($error)): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if(isset($info)): ?>
    <div class="alert alert-info"><?= $info ?></div>
    <?php endif; ?>

    <div class="row">
      <div class="col-sm-12 col-md-6">
        <!-- Cerveja -->

        <div class="row mb-3">
          <div class="col-sm">
            <h5>Fichas de Cerveja</h5>
          </div>
          <div class="col-sm-auto">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
              <button type="submit" name="limpafichas" value="cerveja" class="btn btn-sm btn-danger">Limpar</button>
            </form>
          </div>
        </div>

        <?php
        $fichas = $usrData['cerveja'];
        $total = $fichas * VALOR_CERVEJA;
        $desconto = $total * (PORCENTAGEM_DESCONTO / 100);
        $totalpag = $total - $desconto;
        ?>
        <table class="table table-striped table-bordered">
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

        <div class="mt-3">
          <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="form-group">
              <label for="addficha">Editar Fichas:</label>
              <div class="input-group">
                <input type="number" class="form-control" id="addficha" placeholder="Quantidade de fichas" name="update[cerveja]" required>
                <div class="input-group-append">
                  <button type="submit" name="update[action]" value="add" class="btn btn-info">Adicionar</button>
                  <button type="submit" name="update[action]" value="remove" class="btn btn-danger">Remover</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="col-sm-12 mb-5 d-md-none"><hr/></div>
      <div class="col-sm-12 col-md-6">
        <!-- Refri/Água -->
        <div class="row mb-3">
          <div class="col-sm">
            <h5>Fichas de Refri/Água</h5>
          </div>
          <div class="col-sm-auto">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
              <button type="submit" name="limpafichas" value="refriagua" class="btn btn-sm btn-danger">Limpar</button>
            </form>
          </div>
        </div>

        <?php
        $fichas = $usrData['refriagua'];
        $total = $fichas * VALOR_REFRIAGUA;
        $desconto = $total * (PORCENTAGEM_DESCONTO / 100);
        $totalpag = $total - $desconto;
        ?>
        <table class="table table-striped table-bordered">
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

        <div class="mt-3">
          <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="form-group">
              <label for="addficha">Editar Fichas:</label>
              <div class="input-group">
                <input type="number" class="form-control" id="addficha" placeholder="Quantidade de fichas" name="update[refriagua]" required>
                <div class="input-group-append">
                  <button type="submit" name="update[action]" value="add" class="btn btn-info">Adicionar</button>
                  <button type="submit" name="update[action]" value="remove" class="btn btn-danger">Remover</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/components/jquery/jquery.min.js"></script>
  <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
