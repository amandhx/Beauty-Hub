<?php
require 'banco.php';

// Conecta e busca todos os serviços
$pdo = Banco::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmtServicos = $pdo->query("SELECT id, servicos, valor FROM tb_servicos ORDER BY servicos ASC");
$servicos = $stmtServicos->fetchAll(PDO::FETCH_ASSOC);

// Processa formulário
$erros = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servicoTexto = $_POST['servico'] ?? '';
    $profissional = $_POST['profissional'] ?? '';
    $data = $_POST['data'] ?? '';
    $hora = $_POST['hora'] ?? '';

    if (empty($servicoTexto)) $erros[] = "Selecione um serviço.";
    if (empty($profissional)) $erros[] = "Selecione o profissional.";
    if (empty($data)) $erros[] = "Selecione a data.";
    if (empty($hora)) $erros[] = "Selecione o horário.";

    // Busca o serviço no banco pelo nome
    if ($servicoTexto) {
        $stmt = $pdo->prepare("SELECT id, valor FROM tb_servicos WHERE servicos = ?");
        $stmt->execute([$servicoTexto]);
        $dadosServico = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dadosServico) {
            $id_servico = $dadosServico['id'];
            $valor = $dadosServico['valor'];
        } else {
            $erros[] = "Serviço não encontrado no banco.";
        }
    }

    // Se não houver erros, insere o agendamento
    if (empty($erros)) {
        $sql = "INSERT INTO tb_agenda (id_servico, data, hora, valor) VALUES (?, ?, ?, ?)";
        $q = $pdo->prepare($sql);
        $q->execute([$id_servico, $data, $hora, $valor]);

        header("Location: principal.php");
        exit;
    }
}

Banco::desconectar();
?>



<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal (homepage)</title>
    <link rel="stylesheet" href="css/agenda.css">
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
 
<h1>AGENDA</h1>
<main class="agenda-container">
    <div class="bloco-agenda">
        <form id="form" action="agenda.php" method="POST">
            <section class="passo-selecao">
                <h2>1. Escolha o Serviço</h2>
                <div class="input-group">
                    <label for="service-filter">Serviço:</label>
                    <select id="service-filter" name="servico" required>
                        <option value="" disabled selected>-- Selecione um Serviço --</option>
    <?php foreach ($servicos as $s): ?>
        <option value="<?= htmlspecialchars($s['servicos']) ?>">
            <?= htmlspecialchars($s['servicos']) ?> (R$ <?= number_format($s['valor'], 2, ',', '.') ?>)
        </option>
    <?php endforeach; ?>
                    </select>
                </div>
                </section>
    
                <section class="passo-data-horario">
                <h2>2. Escolha a Data e Hora</h2>
    
                <div class="calendario">
                    <div class="mes-titulo">Outubro 2025</div>
                    <div class="dias-semana">
                        <span>Dom</span><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span><span>Sáb</span>
                    </div>
                    <div class="calendario-grid">
                        <div class="day other-month">28</div><div class="day other-month">29</div><div class="day other-month">30</div><div class="day other-month">01</div>
                        <div class="day available" data-date="2025-10-02">02</div><div class="day available" data-date="2025-10-03">03</div><div class="day available" data-date="2025-10-04">04</div>
                        <div class="day available">05</div><div class="day available" data-date="2025-10-06">06</div><div class="day available" data-date="2025-10-07">07</div><div class="day available" data-date="2025-10-08">08</div>
                        <div class="day available" data-date="2025-10-09">09</div><div class="day available" data-date="2025-10-10">10</div><div class="day available" data-date="2025-10-11">11</div><div class="day available">12</div>
                        <div class="day available" data-date="2025-10-13">13</div><div class="day available" data-date="2025-10-14">14</div><div class="day available" data-date="2025-10-15">15</div><div class="day available" data-date="2025-10-16">16</div>
                        <div class="day available" data-date="2025-10-17">17</div><div class="day available" data-date="2025-10-18">18</div><div class="day available">19</div><div class="day available" data-date="2025-10-20">20</div>
                        <div class="day available" data-date="2025-10-21">21</div><div class="day available" data-date="2025-10-22">22</div><div class="day available" data-date="2025-10-23">23</div><div class="day available" data-date="2025-10-24">24</div>
                        <div class="day available" data-date="2025-10-25">25</div><div class="day available">26</div><div class="day available" data-date="2025-10-27">27</div><div class="day available" data-date="2025-10-28">28</div>
                        <div class="day available" data-date="2025-10-29">29</div><div class="day available" data-date="2025-10-30">30</div><div class="day available" data-date="2025-10-31">31</div>
                    </div>
                </div>
            
                <div class="time-slots-section">
                    <h3 id="horarios-titulo">Selecione uma data acima.</h3>
                    <div id="time-slots-container" class="time-slots-list">
                        <p class="placeholder-text">Selecione uma data para ver os horários disponíveis.</p>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <div class="bloco-resumo">
        <section class="resumo-agendamento">
            <form id="form" action="agenda.php" method="POST">
                <h2>Resumo da Reserva</h2>
                <div class="resumo-detalhes">
                    <p onload="" id="resumo-servico-nome" >Serviço: --</p>
                    <input type="hidden" name="servico" id="Inputservico" value="">
                    <p id="resumo-profissional-nome">Profissional: Wanessa</p>
                    <input type="hidden" name="profissional" id="Inputprofissional" value="">
                    <p id="resumo-data-selecionada" >Data: --</p>
                    <input type="hidden" name="data" id="Inputdata" value="">
                    <p id="resumo-horario-selecionado" >Horário: --</p>
                    <input type="hidden" name="hora" id="Inputhora" value="">
                    <p class="total-estimado" name="totalestimado">Total Estimado: <strong>R$ 0,00</strong></p>
                </div>

                <button class="finalizar-btn" type="submit" onclick="transferirTexto()">Enviar</button>
                <script>
                    function transferirTexto() {
                        const servico = document.getElementById('resumo-servico-nome').innerText;
                        document.getElementById('Inputservico').value = servico;
                        const profissional = document.getElementById('resumo-profissional-nome').innerText;
                        document.getElementById('Inputprofissional').value = profissional;
                        const data = document.getElementById('resumo-data-selecionada').innerText;
                        document.getElementById('Inputdata').value = data;
                        const hora = document.getElementById('resumo-horario-selecionado').innerText;
                        document.getElementById('Inputhora').value = hora;
                        }
                </script>
            </form>
        </section>
    </div>       
</main>
 
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
 
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. Seletores ---
        const serviceSelect = document.getElementById('service-filter');
        const days = document.querySelectorAll('.calendario-grid .day.available');
        const iniciarConfirmacaoBtn = document.getElementById('iniciar-confirmacao-btn');
        const dadosPessoaisSection = document.getElementById('dados-pessoais-section');
 
        // Seletores de Horário Dinâmico
        const horariosTitulo = document.getElementById('horarios-titulo');
        const timeSlotsContainer = document.getElementById('time-slots-container');
 
        // Seletores do Resumo
        const resumoServicoNome = document.getElementById('resumo-servico-nome');
        const resumoDataSelecionada = document.getElementById('resumo-data-selecionada');
        const resumoHorarioSelecionado = document.getElementById('resumo-horario-selecionado');
        const resumoProfissionalNome = document.getElementById('resumo-profissional-nome');
        const resumoTotal = document.querySelector('.total-estimado');
 
        // Variáveis de Estado
        let dataSelecionada = null;
        let horarioSelecionado = null;
 
        // --- 2. Agenda Simulada (Simula o Banco de Dados) ---
        const AgendaSimulada = {
            // Horários Padrão: Das 9h às 18h, com intervalos no meio
            'padrao': [
                '09:00', '10:30', '12:00', // Manhã
                '14:00', '15:30', '17:00'  // Tarde
            ],
            // Dias com Horário Reduzido (Exemplo: Sábados)
            'reduzido': [
                '09:00', '10:30', '12:00'
            ],
            // Horários Ocupados (Simulação de agendamentos existentes)
            'ocupacao': {
                '2025-10-02': ['10:30', '15:30'], // Dia 2 de Outubro tem 2 horários ocupados
                '2025-10-07': ['14:00'],          // Dia 7 de Outubro tem 1 horário ocupado
                '2025-10-25': ['09:00', '10:30', '12:00'] // Dia 25 quase cheio
            }
        };
 
        // Simulação de um banco de dados de serviços
        const Servicos = {
            'corte-feminino': { nome: 'Designer de Sobrancelhas', preco: 45.00, duracao: 60 },
            'manicure-pedicure': { nome: 'Lash Designer', preco: 90.00, duracao: 90 },
            'mechas-platinadas': { nome: 'Micropigmentação', preco: 250.00, duracao: 150 },
            '': { nome: 'Nenhum serviço selecionado.', preco: 0.00 }
        };
 
        // --- 3. Renderiza Horários no HTML ---
        const renderTimeSlots = (data) => {
            timeSlotsContainer.innerHTML = ''; // Limpa os horários anteriores
            horarioSelecionado = null; // Zera a seleção
            resumoHorarioSelecionado.textContent = 'Horário: --';
           
            // Determina se o dia é Sábado para usar horário reduzido
            const dataObj = new Date(data + 'T12:00:00');
            const diaSemana = dataObj.getDay(); // 0=Dom, 6=Sáb
            const horariosDisponiveis = (diaSemana === 6) ? AgendaSimulada.reduzido : AgendaSimulada.padrao;
 
            const ocupados = AgendaSimulada.ocupacao[data] || [];
 
            horariosTitulo.textContent = `Horários disponíveis em ${dataObj.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' })}:`;
           
            if (horariosDisponiveis.length === 0) {
                timeSlotsContainer.innerHTML = '<p>Nenhum horário disponível neste dia.</p>';
                return;
            }
 
            horariosDisponiveis.forEach(time => {
                const isUnavailable = ocupados.includes(time);
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `time-slot ${isUnavailable ? 'unavailable' : ''}`;
                button.setAttribute('data-time', time);
                button.disabled = isUnavailable;
                button.textContent = isUnavailable ? `${time} (Ocupado)` : time;
 
                if (!isUnavailable) {
                    // Adiciona o Event Listener para os botões recém-criados
                    button.addEventListener('click', handleTimeSlotClick);
                }
               
                timeSlotsContainer.appendChild(button);
            });
           
            updateResumoAndButton();
        };
       
        // --- 4. Handler de Clique no Horário ---
        const handleTimeSlotClick = (e) => {
            const slot = e.currentTarget;
 
            if (!serviceSelect.value) {
                alert('Por favor, selecione um Serviço primeiro!');
                return;
            }
           
            // 1. Remove seleção de todos e adiciona ao clicado
            document.querySelectorAll('.time-slots-list .time-slot').forEach(s => s.classList.remove('selected'));
            slot.classList.add('selected');
 
            // 2. Salva o horário
            horarioSelecionado = slot.getAttribute('data-time');
            resumoHorarioSelecionado.textContent = `Horário: ${horarioSelecionado}`;
           
            // 3. Atualiza o resumo
            updateResumoAndButton();
        };
 
        // --- 5. Função de Atualização do Resumo e Botão ---
        const updateResumoAndButton = () => {
            // ... (A lógica de atualização do resumo e habilitação do botão permanece a mesma)
            const selectedValue = serviceSelect.value;
            const serviceData = Servicos[selectedValue] || Servicos[''];
 
            resumoServicoNome.textContent = serviceData.nome;
            resumoProfissionalNome.textContent = 'Profissional: Wanessa';
           
            const precoFormatado = serviceData.preco.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
            resumoTotal.innerHTML = `Total Estimado: <strong>${precoFormatado}</strong>`;
 
            if (selectedValue && dataSelecionada && horarioSelecionado) {
                iniciarConfirmacaoBtn.disabled = false;
            } else {
                iniciarConfirmacaoBtn.disabled = true;
            }
        };
 
        // --- 6. Event Listeners para Interação ---
 
        // A. Seleção de Serviço
        serviceSelect.addEventListener('change', updateResumoAndButton);
 
        // B. Seleção de Dia
        days.forEach(day => {
            day.addEventListener('click', () => {
                // ... (Lógica de seleção de dia)
                days.forEach(d => d.classList.remove('selected'));
                day.classList.add('selected');
               
                dataSelecionada = day.getAttribute('data-date');
                const dataObj = new Date(dataSelecionada + 'T12:00:00');
                const diaSemana = dataObj.toLocaleDateString('pt-BR', { weekday: 'short' });
                const diaMes = day.textContent.trim();
 
                resumoDataSelecionada.textContent = `Data: ${diaSemana}, ${diaMes}/Out/2025`;
 
                // CHAMA A FUNÇÃO QUE CRIA OS HORÁRIOS
                renderTimeSlots(dataSelecionada);
               
                updateResumoAndButton();
            });
        });
 
        // C. Botão de Confirmação Final
        iniciarConfirmacaoBtn.addEventListener('click', (e) => {
            e.preventDefault();
            dadosPessoaisSection.classList.remove('hidden');
            dadosPessoaisSection.scrollIntoView({ behavior: 'smooth' });
        });
 
        // --- 7. Inicialização ---
        updateResumoAndButton();
    });
</script>

</body>
</html>