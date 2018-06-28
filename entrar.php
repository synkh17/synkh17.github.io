<?php
session_start(); // Inicia a sessão

// Verifica se está logado
if(isset($_SESSION['logado'])) {
  header('location: index.php');
  exit;
}
require_once 'conexao.php';

// Requisição de login
if(isset($_POST['matricula'], $_POST['senha'])) {
  // Efetuar login
  $matricula = $_POST['matricula'];
  $senha = $_POST['senha'];

  $sql = "SELECT f.matricula, f.senha FROM funcionario f WHERE f.matricula=?";
  $stmt = $PDO->prepare($sql);
  if($stmt->execute(array($matricula))) {
    // Usuário encontrado, validar senha
    $data = $stmt->fetch();
    if(password_verify($senha, $data['senha'])) {
      $_SESSION['logado'] = $data['matricula'];
      header('location: index.php');
      exit;
    }
  }
  // Usuário ou senha não conferem
  header('location: entrar.php?login_invalido=1');
}

// Desconectado
if(isset($_GET['logout'])) {
  $info = "Desconectado.";
}
// Conta criada
if(isset($_GET['conta_criada'], $_GET['matricula'])) {
  $matricula = $_GET['matricula'];
  $info = "Conta criada com sucesso.<br/>";
  $info .= "Matricula Nº: <strong>$matricula</strong>.";
}
// Login inválido
if(isset($_GET['login_invalido'])) {
  $error = "Usuário ou senha incorretos.";
}
// Erro de usuário
if(isset($_GET['usr_error'])) {
  $error = "Ops! Algo deu errado, entre novamente!";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Copa - CTG Eduardo Muller | Entrar</title>

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

  <div class="overlay">
    <img src="img/bg.jpg" alt="">
  </div>

  <div class="masthead">
    <div class="masthead-bg"></div>
    <div class="container h-100">
      <div class="row h-100">
        <div class="col-12 my-auto">
          <div class="masthead-content text-white py-5 mb-5">
            <h1 class="mb-3">CTG Eduardo Muller</h1>
            <p class="mb-0">Entre para visualizar seus dados</p>
            <p class="mb-5">ou cadastre-se clicando <a href="cadastro.php">aqui</a>.</p>
            
            <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if(isset($info)): ?>
            <div class="alert alert-info"><?= $info ?></div>
            <?php endif; ?>

            <form name="f_log" action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
              <div class="form-group">
                <label for="matricula">Matricula:</label>
                <input type="text" class="form-control" id="matricula" placeholder="Informe o número da sua matrícula" name="matricula" value="<?= isset($matricula) ? $matricula : '' ?>" required>
              </div>
              <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="Informe sua senha" <?= isset($matricula) ? 'autofocus' : '' ?>>
              </div>

              <button class="btn btn-secondary" type="submit">Entre!</button>
            </form>
            
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="social-icons">
    <ul class="list-unstyled text-center mb-0">
      <li class="list-unstyled-item">
        <a href="https://www.facebook.com/ctgedumuller" target="_blank">
          <i class="fa fa-facebook-f"></i>
        </a>
      </li>
    </ul>
  </div>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/components/jquery/jquery.min.js"></script>
  <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
