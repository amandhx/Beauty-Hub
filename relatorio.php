<?php
session_start();
if ($_SESSION["usuario_tipo"] !== "admin") {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_beauty_hub";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// FILTRO DE DATAS
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';

$where = "";
if (!empty($data_inicio) && !empty($data_fim)) {
    $where = "WHERE data BETWEEN '$data_inicio' AND '$data_fim'";
}

// TOTAL DO PERÍODO OU MÊS ATUAL
$sql_total = "SELECT SUM(valor) AS total_periodo FROM tb_agenda $where";
$result_total = $conn->query($sql_total);
$total_periodo = $result_total->fetch_assoc()['total_periodo'] ?? 0;

// DADOS GRÁFICO (últimos 6 meses)
$sql_grafico = "
SELECT DATE_FORMAT(data, '%m/%Y') AS mes, SUM(valor) AS total
FROM tb_agenda
GROUP BY mes
ORDER BY data DESC
LIMIT 6";
$result_grafico = $conn->query($sql_grafico);
$meses = [];
$totais = [];
while ($row = $result_grafico->fetch_assoc()) {
    $meses[] = $row['mes'];
    $totais[] = $row['total'];
}
$meses = array_reverse($meses);
$totais = array_reverse($totais);

// TOTAL POR TIPO DE SERVIÇO
$sql_tipos = "
SELECT s.servicos AS tipo_servico, SUM(a.valor) AS total
FROM tb_agenda a
JOIN tb_servicos s ON a.id_servico = s.id
GROUP BY s.servicos";
$result_tipos = $conn->query($sql_tipos);
$tipos_labels = [];
$tipos_totais = [];
while ($row = $result_tipos->fetch_assoc()) {
    $tipos_labels[] = $row['tipo_servico'];
    $tipos_totais[] = $row['total'];
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro</title>
    <link rel="stylesheet" href="css/relatorio.css">
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

<div class="finance-container">
    <h1>📊 Relatório Financeiro</h1>

    <!-- FILTRO -->
    <form method="GET" class="form-filtro">
        <label>De:</label>
        <input type="date" name="data_inicio" value="<?= $data_inicio ?>">
        <label>Até:</label>
        <input type="date" name="data_fim" value="<?= $data_fim ?>">
        <button type="submit">Filtrar</button>
    </form>

    <!-- TOTAL PERÍODO -->
    <div class="card-finance">
        <h2>Ganhos no Período</h2>
        <p class="valor">R$ <?= number_format($total_periodo, 2, ',', '.'); ?></p>
        <button onclick="window.print()" class="btn-pdf">📄 Exportar PDF</button>
    </div>

    <!-- GRÁFICO DE GANHOS -->
    <h3>Ganhos por Mês</h3>
    <canvas id="graficoMes" height="150"></canvas>

    <!-- GRÁFICO POR SERVIÇO -->
    <h3>Ganhos por Tipo de Serviço</h3>
    <canvas id="graficoServicos" height="150"></canvas>

    <a href="admin_dashboard.php" class="btn-voltar">⬅ Voltar ao Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const meses = <?= json_encode($meses) ?>;
const totais = <?= json_encode($totais) ?>;
const tipos = <?= json_encode($tipos_labels) ?>;
const valoresTipos = <?= json_encode($tipos_totais) ?>;

// Gráfico 1 - Últimos meses
new Chart(document.getElementById("graficoMes"), {
    type: "line",
    data: {
        labels: meses,
        datasets: [{
            label: "Ganhos (R$)",
            data: totais
        }]
    }
});

// Gráfico 2 - Serviços
new Chart(document.getElementById("graficoServicos"), {
    type: "bar",
    data: {
        labels: tipos,
        datasets: [{
            label: "Ganhos (R$)",
            data: valoresTipos
        }]
    }
});
</script>


</body>
</html>
