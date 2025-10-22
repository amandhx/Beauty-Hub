<?php
session_start();

// Se não estiver logado, volta para login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

// Se não for admin, também bloqueia
if ($_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<!-- Menu de Navegação -->
<nav>
  <div class="logo"><a href="#"><img src="img/logotipo.png" alt=""></a></div>
    <ul>
        <li><a href="admin_dashboard.php">Home</a></li>
        <li><a href="relatorio.php">Financeiro</a></li>
        <li><a href="servicos_admin.php">Serviços</a></li>
        <li><a href="agenda_admin.php">Agenda</a></li>
        <li><a href="clientes.php">Clientes</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="container">
    <h1>Bem-vinda, <?php echo $_SESSION["usuario_nome"]; ?> 👑</h1>
    <p class="subtitulo">O que você deseja gerenciar hoje?</p>

    <div class="card-grid">
        <a href="relatorio.php" class="card">
            <h2>📊 Relatório Financeiro</h2>
            <p>Veja dados e estatísticas financeiras detalhadas.</p>
        </a>

        <a href="servicos_admin.php" class="card">
            <h2>🧴 Serviços</h2>
            <p>Adicione, edite ou remova serviços disponíveis.</p>
        </a>

        <a href="agenda_admin.php" class="card">
            <h2>📅 Agenda</h2>
            <p>Gerencie horários e vizualize sua agenda.</p>
        </a>

        <a href="clientes.php" class="card">
            <h2>👥 Clientes</h2>
            <p>Visualize todos seus clientes.</p>
        </a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> 2025 Beauty Hub. Todos os direitos reservados.</p>
    </div>
</footer>

</body>
</html>
