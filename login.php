<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_beauty_hub";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM tb_usuarios WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($password, $usuario['senha'])) {
            $_SESSION["usuario_id"] = $usuario['id'];
            $_SESSION["usuario_nome"] = $usuario['username'];
            $_SESSION["usuario_tipo"] = $usuario['tipo'];

            if ($usuario['tipo'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: cliente_dashboard.php");
            }
            exit;
        } else {
            echo "<script>alert('Senha incorreta!');</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado!');</script>";
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
    <title>Página Login (cliente)</title>
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

<!-- MENU -->
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

<!-- LOGIN -->
<div class="container-login">
    <h2>Login</h2>

    <?php if (!empty($erro)): ?>
        <p style="color:red;"><?= $erro ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="input-group">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="input-group">
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Entrar</button>
    </form>
    <div class="register-link">
        <p>Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
    </div>
</div>

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
                <a href="">Facebook: </a><br><br>
                <a href="">Instagram: @wanessa_studiowf</a><br><br>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy;2025 Beauty Hub. Todos os direitos reservados.</p>
    </div>
</footer>

</body>
</html>