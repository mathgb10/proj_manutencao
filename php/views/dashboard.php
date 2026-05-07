<?php require __DIR__ . '/../controllers/validar_acesso.php'; ?>
<?php require_once __DIR__ . '/../configs/conexao.php'; ?>
<?php require __DIR__ . '/../components/modals/all_modals.php'; ?>

<?php
// --- Lógica de Dados ---
// 1. O.S. Ativas
$sqlOS = "SELECT os.*, u.nome as tecnico 
          FROM ordens_servico os 
          LEFT JOIN usuarios u ON os.responsavel_id = u.id 
          WHERE os.tipo = 'Corretivo' AND os.status NOT IN ('Arquivada', 'Recusada')
          ORDER BY os.criado_em DESC LIMIT 6";
$resOS = $conn->query($sqlOS);

// 2. Preventivas (Cálculo de Vencimento)
$sqlMaq = "SELECT m.id, m.denominacao, m.numero_identificacao,
          (SELECT MAX(data_realizada) FROM historico_manutencao WHERE maquina_id = m.id) as ultima,
          (SELECT MIN(CASE 
                WHEN frequencia = 'diario' THEN 1 WHEN frequencia = 'semanal' THEN 7
                WHEN frequencia = 'quinzenal' THEN 15 WHEN frequencia = 'mensal' THEN 30
                WHEN frequencia = 'trimestral' THEN 90 WHEN frequencia = 'semestral' THEN 180
                WHEN frequencia = 'anual' THEN 365 ELSE 30 END) 
           FROM checklist_itens WHERE maquina_id = m.id) as intervalo
          FROM maquinas m";
$resMaq = $conn->query($sqlMaq);

$vencidas = [];
$hoje = date('Y-m-d');
if ($resMaq) {
    while ($m = $resMaq->fetch_assoc()) {
        $int = $m['intervalo'] ?: 30;
        $m['data_venc'] = 'S/ DATA'; // Default
        if (!$m['ultima']) {
            $vencidas[] = $m;
        } else {
            $proxima = date('Y-m-d', strtotime($m['ultima'] . " + $int days"));
            if ($proxima < $hoje) {
                $m['data_venc'] = date('d/m', strtotime($proxima));
                $vencidas[] = $m;
            }
        }

    }
}

// 3. Contadores
$count_maq = $conn->query("SELECT COUNT(*) as t FROM maquinas")->fetch_assoc()['t'];
$count_abertas = $conn->query("SELECT COUNT(*) as t FROM ordens_servico WHERE status IN ('Em Aberto', 'Aguardando Aprovação')")->fetch_assoc()['t'];
$count_andamento = $conn->query("SELECT COUNT(*) as t FROM ordens_servico WHERE status = 'Aceita'")->fetch_assoc()['t'];
$count_total_manut = $conn->query("SELECT COUNT(*) as t FROM historico_manutencao")->fetch_assoc()['t'];

// 4. Atividade Recente (Histórico)
$sqlRecente = "SELECT h.*, m.denominacao 
               FROM historico_manutencao h 
               JOIN maquinas m ON h.maquina_id = m.id 
               ORDER BY h.data_realizada DESC LIMIT 3";
$resRecente = $conn->query($sqlRecente);

// 5. Dados para Novos Gráficos
// Tendência (Últimos 7 dias)
$tendenciaLabels = [];
$tendenciaData = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i days"));
    $tendenciaLabels[] = date('d/m', strtotime($d));
    $tendenciaData[] = $conn->query("SELECT COUNT(*) as t FROM ordens_servico WHERE DATE(criado_em) = '$d'")->fetch_assoc()['t'];
}

// Top Máquinas Problemáticas
$sqlTopMaq = "SELECT patrimonio, COUNT(*) as total 
              FROM ordens_servico 
              GROUP BY patrimonio ORDER BY total DESC LIMIT 5";
$resTopMaq = $conn->query($sqlTopMaq);
$topMaqLabels = [];
$topMaqData = [];
while ($tm = $resTopMaq->fetch_assoc()) {
    $topMaqLabels[] = $tm['patrimonio'] ?: 'N/A';
    $topMaqData[] = $tm['total'];
}
?>



<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistema de Manutenção</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .btn-alert-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .btn-alert-action:hover {
            background: var(--corBase);
            transform: translateY(-2px);
        }

        .btn-alert-checklist {
            background: var(--corDestaque);
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main dashboard-page">
        <?php require __DIR__ . '/../components/header.php'; ?>

        <!-- KPIs SUPERIORES -->
        <div class="kpi-container">
            <div class="kpi-card" style="--card-color: var(--corBase);">
                <div class="kpi-icon"><i class="bi bi-cpu"></i></div>
                <div class="kpi-content">
                    <p>Ativos Totais</p>
                    <h3><?= $count_maq ?></h3>
                </div>
            </div>
            <div class="kpi-card" style="--card-color: #ffc107;">
                <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
                <div class="kpi-content">
                    <p>O.S. Pendentes</p>
                    <h3><?= $count_abertas ?></h3>
                </div>
            </div>
            <div class="kpi-card" style="--card-color: #17a2b8;">
                <div class="kpi-icon"><i class="bi bi-wrench"></i></div>
                <div class="kpi-content">
                    <p>Em Execução</p>
                    <h3><?= $count_andamento ?></h3>
                </div>
            </div>
            <div class="kpi-card" style="--card-color: #28a745;">
                <div class="kpi-icon"><i class="bi bi-check2-circle"></i></div>
                <div class="kpi-content">
                    <p>Total Realizadas</p>
                    <h3><?= $count_total_manut ?></h3>
                </div>
            </div>
        </div>


        <!-- GRID PRINCIPAL -->
        <div class="dashboard-grid">

            <!-- COLUNA PRINCIPAL: OPERAÇÕES E RECORRÊNCIA -->
            <div class="main-col" style="display: flex; flex-direction: column; gap: 25px;">

                <!-- CENTRO DE OPERAÇÕES -->
                <div class="dash-panel">
                    <div class="dash-panel-header">
                        <h2><i class="bi bi-activity"></i> Centro de Manutenção Corretiva</h2>
                        <button class="btn-dash-action" onclick="window.location.href='gerencias_os.php'">Nova
                            O.S.</button>
                    </div>
                    <div class="dash-panel-content">
                        <table class="os-table">
                            <thead>
                                <tr>
                                    <th>Patrimônio</th>
                                    <th>Descrição do Problema</th>
                                    <th>Técnico</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($resOS && $resOS->num_rows > 0): ?>
                                    <?php while ($os = $resOS->fetch_assoc()): ?>
                                        <tr>
                                            <td class="os-patrimonio"><?= htmlspecialchars($os['patrimonio'] ?: 'N/A') ?></td>
                                            <td><?= htmlspecialchars(mb_strimwidth($os['descricao'], 0, 50, "...")) ?></td>
                                            <td style="opacity: 0.8;"><?= htmlspecialchars($os['tecnico'] ?: 'A definir') ?>
                                            </td>
                                            <td>
                                                <span
                                                    class="os-status-badge <?= $os['status'] == 'Aceita' ? 'status-aceito' : 'status-aberto' ?>">
                                                    <?= $os['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 40px; opacity: 0.5;">Nenhuma
                                            operação ativa no momento.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ANALYTICS: RECORRÊNCIA (Movido para a Esquerda) -->
                <div class="dash-panel">
                    <div class="dash-panel-header">
                        <h2><i class="bi bi-bar-chart-steps"></i> Recorrência de Falhas (Top 5 Máquinas)</h2>
                    </div>
                    <div class="dash-panel-content">
                        <div class="chart-box" style="height: 250px;">
                            <canvas id="topMaqChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>


            <!-- COLUNA DIREITA: ANALYTICS E ALERTAS -->
            <div class="side-analytics">

                <!-- ANALYTICS: DISTRIBUIÇÃO -->
                <div class="dash-panel">
                    <div class="dash-panel-header">
                        <h2><i class="bi bi-pie-chart-fill"></i> Saúde da Frota</h2>
                    </div>
                    <div class="chart-box" style="height: 220px;">
                        <canvas id="mainChart"></canvas>
                    </div>
                </div>



                <!-- ATIVIDADE RECENTE -->
                <div class="dash-panel" style="margin-top: 20px;">
                    <div class="dash-panel-header">
                        <h2><i class="bi bi-clock-history"></i> Histórico Recente</h2>
                    </div>
                    <div class="dash-panel-content">
                        <div class="alert-list">
                            <?php if ($resRecente && $resRecente->num_rows > 0): ?>
                                <?php while ($h = $resRecente->fetch_assoc()): ?>
                                    <div class="alert-item"
                                        style="background: rgba(40, 167, 69, 0.05); border-color: rgba(40, 167, 69, 0.1);">
                                        <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                                        <div class="alert-info">
                                            <strong><?= htmlspecialchars($h['denominacao']) ?></strong>
                                            <small><?= date('d/m/Y', strtotime($h['data_realizada'])) ?></small>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p style="text-align: center; font-size: 0.8rem; opacity: 0.5;">Nenhum histórico encontrado.
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ALERTAS CRÍTICOS -->
                <div class="dash-panel" style="margin-top: 20px;">
                    <div class="dash-panel-header">
                        <h2><i class="bi bi-shield-fill-exclamation" style="color: var(--status-danger);"></i> Alertas
                            Críticos</h2>
                        <button class="btn-link" onclick="window.location.href='preventiva.php'">Ver Todos</button>
                    </div>
                    <div class="dash-panel-content">
                        <div class="alert-list">
                            <?php if (count($vencidas) > 0): ?>
                                <?php foreach (array_slice($vencidas, 0, 3) as $v): ?>
                                    <div class="alert-item" style="flex-direction: column; align-items: flex-start; gap: 10px;">
                                        <div style="display: flex; align-items: center; gap: 15px; width: 100%;">
                                            <i class="bi bi-exclamation-triangle-fill" style="color: var(--status-danger);"></i>
                                            <div class="alert-info" style="flex: 1;">
                                                <strong><?= htmlspecialchars($v['denominacao']) ?></strong>
                                                <small><?= htmlspecialchars($v['numero_identificacao']) ?></small>
                                            </div>
                                        </div>
                                        <div
                                            style="display: flex; gap: 8px; width: 100%; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px;">
                                            <button class="btn-alert-action btn-alert-checklist"
                                                onclick="openChecklist('<?= addslashes($v['denominacao']) ?>', <?= $v['id'] ?>)">
                                                <i class="bi bi-list-check"></i> Abrir Checklist
                                            </button>
                                            <button class="btn-alert-action"
                                                onclick="openHistory('<?= addslashes($v['denominacao']) ?>', <?= $v['id'] ?>)">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="text-align: center; font-size: 0.8rem; opacity: 0.5;">Sistema operando
                                    normalmente.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <script src="../../js/scripts.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cores padrão
            const colorBase = getComputedStyle(document.documentElement).getPropertyValue('--corBase').trim() || '#fc2323';

            // Gráfico de Pizza (Distribuição)
            new Chart(document.getElementById('mainChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Abertas', 'Andamento', 'OK'],
                    datasets: [{
                        data: [<?= $count_abertas ?>, <?= $count_andamento ?>, <?= $count_maq - ($count_abertas + $count_andamento) ?>],
                        backgroundColor: ['#ffc107', '#17a2b8', 'rgba(255,255,255,0.05)'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: { legend: { position: 'bottom', labels: { color: '#fff', font: { size: 10 } } } }
                }
            });

            // Gráfico de Barras (Top Máquinas)
            new Chart(document.getElementById('topMaqChart'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($topMaqLabels) ?>,
                    datasets: [{
                        label: 'O.S. Geradas',
                        data: <?= json_encode($topMaqData) ?>,
                        backgroundColor: colorBase,
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#888' } },
                        y: { grid: { display: false }, ticks: { color: '#fff' } }
                    }
                }
            });
        });
    </script>


    <?php if (isset($_SESSION['user_senha_padrao']) && $_SESSION['user_senha_padrao'] == 1): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('changePassword');
                if (modal) modal.style.display = 'flex';
            });
        </script>
    <?php endif; ?>
</body>

</html>