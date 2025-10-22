<?php
session_start();

// Protege: apenas admin
if (!isset($_SESSION["usuario_id"]) || $_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Config DB
$servername = "localhost";
$db_user = "root";
$db_pass = "";
$dbname = "db_beauty_hub";

$conn = new mysqli($servername, $db_user, $db_pass, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// ==> Adicionar bloqueio
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_bloqueio'])) {
    // Recebe datetimes do formulário; garantia mínima de trimming
    $data_inicio = trim($_POST['data_inicio']);
    $data_fim = trim($_POST['data_fim']);

    // Validações simples
    if (empty($data_inicio) || empty($data_fim)) {
        $msg = "Preencha data/hora de início e fim.";
    } elseif (strtotime($data_inicio) >= strtotime($data_fim)) {
        $msg = "A data/hora de início deve ser anterior à data/hora de fim.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bloqueios (data_inicio, data_fim) VALUES (?, ?)");
        $stmt->bind_param("ss", $data_inicio, $data_fim);
        if ($stmt->execute()) {
            $msg = "Horário bloqueado com sucesso!";
        } else {
            $msg = "Erro ao bloquear horário. Tente novamente.";
        }
        $stmt->close();
    }
}

// ==> Remover bloqueio
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_bloqueio'])) {
    $id = intval($_POST['delete_bloqueio']);
    $stmt = $conn->prepare("DELETE FROM bloqueios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $msg = "Bloqueio removido.";
    } else {
        $msg = "Erro ao remover bloqueio.";
    }
    $stmt->close();
}

// Busca bloqueios (ordenados do mais próximo ao mais distante)
$result = $conn->query("SELECT * FROM bloqueios ORDER BY data_inicio ASC");
$bloqueios = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $bloqueios[] = $row;
    }
    $result->free();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Gerenciar Agenda - Admin</title>
<link rel="stylesheet" href="css/login.css"> <!-- ou seu CSS -->

<style>
.container { max-width: 900px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 6px; }
h1 { margin-top: 0; }
.form-row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.form-row label { min-width:120px; }
.form-row input { padding:8px; border:1px solid #ccc; border-radius:4px; }
button { padding:8px 12px; border: none; border-radius:4px; background:#007bff; color:#fff; cursor:pointer; }
button.delete { background:#dc3545; }
.msg { margin:10px 0; color:#006600; }
.error { margin:10px 0; color:#b30000; }
table { width:100%; border-collapse: collapse; margin-top: 15px; }
th, td { padding:8px; border:1px solid #eee; text-align:left; }
</style>

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
    <h1>Gerenciar Agenda (Admin)</h1>

    <?php if (!empty($msg)): ?>
        <p class="msg"><?php echo htmlspecialchars($msg); ?></p>
    <?php endif; ?>

    <h2>Bloquear período</h2>
    <!-- Use datetime-local para data+hora (ajusta no navegador) -->
    <form method="post" class="form-row">
        <label for="data_inicio">Início:</label>
        <input type="datetime-local" id="data_inicio" name="data_inicio" required>

        <label for="data_fim">Fim:</label>
        <input type="datetime-local" id="data_fim" name="data_fim" required>

        <button type="submit" name="add_bloqueio">Bloquear</button>
    </form>

    <h2>Bloqueios cadastrados</h2>
    <?php if (count($bloqueios) === 0): ?>
        <p>Nenhum bloqueio cadastrado.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($bloqueios as $b): ?>
                <tr>
                    <td><?php echo (int)$b['id']; ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($b['data_inicio']))->format('d/m/Y H:i')); ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($b['data_fim']))->format('d/m/Y H:i')); ?></td>
                    <td><?php echo htmlspecialchars((new DateTime($b['criado_em']))->format('d/m/Y H:i')); ?></td>
                    <td>
                        <form method="post" style="display:inline" onsubmit="return confirm('Remover este bloqueio?');">
                            <input type="hidden" name="delete_bloqueio" value="<?php echo (int)$b['id']; ?>">
                            <button type="submit" class="delete">Remover</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

</body>
</html>
