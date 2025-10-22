<?php
// CONFIGURAÇÕES DE CONEXÃO
$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "db_beauty_hub";

// Conecta ao banco de dados
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Verifica se as senhas coincidem
    if ($password !== $confirm_password) {
        echo "<script>alert('As senhas não coincidem!'); window.history.back();</script>";
        exit;
    }

    // Criptografa a senha
    $senha_hash = password_hash($password, PASSWORD_DEFAULT);

    // Verifica se o usuário já existe
    $sql_verifica = "SELECT * FROM tb_usuarios WHERE username = ? OR email = ?";
    $stmt_verifica = $conn->prepare($sql_verifica);
    $stmt_verifica->bind_param("ss", $username, $email);
    $stmt_verifica->execute();
    $resultado = $stmt_verifica->get_result();

    if ($resultado->num_rows > 0) {
        echo "<script>alert('Usuário ou e-mail já cadastrado!'); window.history.back();</script>";
        exit;
    }

    // Insere o novo usuário
    $sql = "INSERT INTO tb_usuarios (username, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $senha_hash);

    if ($stmt->execute()) {
        echo "<script>alert('Cadastro realizado com sucesso! Faça login.'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar. Tente novamente.');</script>";
    }

    $stmt->close();
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Cadastro (cliente)</title>
    <link rel="stylesheet" href="./css/cadastro.css">
</head>

<body>

<!-- DROPDOWN (MENU DE NAVEGAÇÃO) -->
<nav>
    <div class="logo"><a href="#"><img src="img/logotipo.png" alt=""></a></div>
    <ul>
        <li><a href="principal.php">Home</a></li>
        <li><a href="sobre.php">Sobre</a></li>
        <li><a href="servicos.php">Serviços</a></li>
        <li><a href="agenda.php">Agenda</a></li>
        <li><a href="login.php">Login</a></li>
    </ul>
</nav>
<br>

<!-- CADRASTRO -->
<div class="container-cadastro">
    <h2>Cadastro</h2>
    <form action="cadastro.php" method="post">
        <div class="input-group">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="input-group">
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="input-group">
            <label for="confirm-password">Confirmar Senha:</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
        </div>
        <button type="submit">Cadastrar</button>
    </form>
    <div class="login-link">
        <p>Já tem uma conta? <a href="login.html">Faça login</a></p>
    </div>
</div>
<br>

<!-- FOOTER -->
<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h4>Contato</h4>
            <p>Email: wanessa.kimura@yahoo.com</p>
            <p>Telefone: (19)99256-0108</p>
        </div>

        <div class="footer-section">
            <h4>Endereço</h4>
            <p>Av. Baden Powell 518 - Nova Europa <br> Campinas-SP</p>
        </div>

        <div class="footer-section">
            <h4>Siga-nos</h4>
            <div class="social-links">
                <a href="">Facebook: </a><br></br>
                <a href="">Instagram: @wanessa_studiowf</a><br></br>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy;2025 Beauty Hub. Todos os direitos reservados.</p>
    </div>
</footer>

<body>
</html>