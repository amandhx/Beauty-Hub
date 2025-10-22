<?php
session_start();

// Se não estiver logado, volta para login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Garante que seja cliente
if ($_SESSION["usuario_tipo"] !== "cliente") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Cliente</title>
    <link rel="stylesheet" href="css/cliente.css">
</head>
<body>

<!-- Navegação -->
<nav>
  <div class="logo"><a href="#"><img src="img/logotipo.png" alt=""></a></div>
  <ul>
      <li><a href="cliente_dashboard.php">Home</a></li>
      <li><a href="agenda.php">Agenda</a></li>
      <li><a href="servicos.php">Serviços</a></li>
      <li><a href="#">Agendamentos</a></li>
      <li><a href="#">Perfil</a></li>
      <li><a href="logout.php">Sair</a></li>
  </ul>
</nav>

<!-- Conteúdo principal -->
<div class="container">
    <h1>Olá, <?php echo $_SESSION["usuario_nome"]; ?> 🌷</h1>
    <p class="subtitulo">Bem-vinda ao seu espaço pessoal!</p>

    <div class="card-grid">
        <a href="agenda.php" class="card">
            <h2>📅 Minha Agenda</h2>
            <p>Confira e agende seus horários com facilidade.</p>
        </a>

        <a href="servicos.php" class="card">
            <h2>💆‍♀️ Meus Agendamentos</h2>
            <p>Vizualize os serviços que você agendou.</p>
        </a>

        <a href="perfil.php" class="card">
            <h2>👤 Meu Dados</h2>
            <p>Atualize suas informações pessoais e preferências.</p>
        </a>

    </div>
</div>

<footer>
  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> Seu Sistema. Todos os direitos reservados.</p>
  </div>
</footer>

</body>
</html>
