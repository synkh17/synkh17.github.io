<?php 
session_start(); // Inicia a sessão

// Verifica se está logado
if(isset($_SESSION['logado'])) {
  header('location: index.php');
  exit;
}
require_once 'conexao.php';

// Requisição de cadastro
if(isset($_POST['cadform'])) {
  if(isset($_POST['cadform']['nome'], $_POST['cadform']['idade'],
  $_POST['cadform']['senha'])) {
    // Efetuar cadastro
    $form = $_POST['cadform'];

    // Gera a matricula
    $matq = $PDO->prepare('SELECT MAX(id) +1 FROM funcionario');
    $matq->execute();
    $matn = $matq->fetch();
    $matricula = date('Y') . $matn[0];
    $form['matricula'] = str_pad($matricula, 10, '0', STR_PAD_RIGHT);

    // Criptografa a senha
    $form['senha'] = password_hash($form['senha'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO funcionario(nome,idade,matricula,senha) VALUES (:nome,:idade,:matricula,:senha)";
    $stmt = $PDO->prepare($sql);
    if($stmt->execute($form)) {
      // Usuário inserido
      header('location: entrar.php?conta_criada=1&matricula=' . $form['matricula']);
      exit;
    } else {
      // Erro ao cadastrar
      $error = "Ocorreu um erro ao efetuar o cadastro.";
      unset($_POST['cadform']);
    }
  } else {
    $error = "Você deve preencher todos os campos.";
    unset($_POST['cadform']);
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Copa - CTG Eduardo Muller | Cadastro</title>

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
            <p class="mb-0">Cadastre-se</p>
            <p class="mb-5">ou entre clicando <a href="entrar.php">aqui</a>.</p>
            
            <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if(isset($info)): ?>
            <div class="alert alert-info"><?= $info ?></div>
            <?php endif; ?>

            <form name="f_cad" action="<?= $_SERVER['PHP_SELF']; ?>" method="POST">
              <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" class="form-control" id="nome" placeholder="Informe apenas seu primeiro nome" name="cadform[nome]" required>
              </div>
              <div class="form-group">
                <label for="idade">Idade:</label>
                <input type="number" class="form-control" id="idade" placeholder="Informe sua idade" name="cadform[idade]" required>
              </div>
              <div class="form-group">
                <label for="senha">Senha</label>
                <input type="text" class="form-control" id="senha" name="cadform[senha]" placeholder="Escolha uma senha">
              </div>

              <button class="btn btn-secondary" type="submit">Cadastre-se!</button>
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
          <i class="fa fa-facebook"></i>
        </a>
      </li>
    </ul>
  </div>

  <!-- Bootstrap core JavaScript -->
  <script src="vendor/components/jquery/jquery.min.js"></script>
  <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
