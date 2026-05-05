<?php require __DIR__ . '\..\controllers\validar_acesso.php'; ?>
<?php require_once __DIR__ . "\..\configs\conexao.php"; ?>
<?php require __DIR__ . '\..\components\modals\all_modals.php'; ?>

<?php
// --- Lógica de Notificações e Status ---

if (!function_exists('calcularStatusDashboard')) {
    function calcularStatusDashboard($conn, $maquina_id)
    {
        // 1. Descobrir a menor frequência (intervalo) configurada para esta máquina
        $sqlFreq = "SELECT MIN(
            CASE 
                WHEN frequencia = 'diario' THEN 1
                WHEN frequencia = 'semanal' THEN 7
                WHEN frequencia = 'quinzenal' THEN 15
                WHEN frequencia = 'mensal' THEN 30
                WHEN frequencia = 'trimestral' THEN 90
                WHEN frequencia = 'semestral' THEN 180
                WHEN frequencia = 'anual' THEN 365
                ELSE 30 
            END
        ) as intervalo FROM checklist_itens WHERE maquina_id = ?";
        
        $stmtF = $conn->prepare($sqlFreq);
        $stmtF->bind_param("i", $maquina_id);
        $stmtF->execute();
        $resF = $stmtF->get_result();
        $intervalo = 30; // Default
        if ($rowF = $resF->fetch_assoc()) {
            if ($rowF['intervalo']) $intervalo = $rowF['intervalo'];
        }
        $stmtF->close();

        // 2. Buscar a última manutenção realizada
        $sql = "SELECT MAX(data_realizada) as ultima FROM historico_manutencao WHERE maquina_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $maquina_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $lastDate = null;
        if ($row = $result->fetch_assoc()) {
            $lastDate = $row['ultima'];
        }
        $stmt->close();

        if (!$lastDate) {
            return ['status' => 'VENCIDO', 'data' => 'S/ REGISTRO'];
        }

        $proxima = date('Y-m-d', strtotime($lastDate . " + $intervalo days"));
        $hoje = date('Y-m-d');
        $seteDias = date('Y-m-d', strtotime('+7 days'));

        if ($proxima < $hoje) {
            return ['status' => 'VENCIDO', 'data' => date('d/m/Y', strtotime($proxima))];
        } elseif ($proxima <= $seteDias) {
            return ['status' => 'PROXIMO', 'data' => date('d/m/Y', strtotime($proxima))];
        }
        return ['status' => 'OK', 'data' => date('d/m/Y', strtotime($proxima))];
    }
}

// Buscar Corretivas Ativas (Em Aberto ou Aceitas)
$sqlCorretivas = "SELECT os.*, u.nome as tecnico 
                  FROM ordens_servico os 
                  LEFT JOIN usuarios u ON os.responsavel_id = u.id 
                  WHERE os.tipo = 'Corretivo' AND os.status NOT IN ('Arquivada', 'Recusada')
                  ORDER BY os.criado_em DESC LIMIT 4";
$resCorretivas = $conn->query($sqlCorretivas);

// Buscar Preventivas (Vencidas e Próximas)
$sqlMaquinas = "SELECT id, denominacao, numero_identificacao, modelo FROM maquinas";
$resMaquinas = $conn->query($sqlMaquinas);

$preventivasVencidas = [];
$preventivasProximas = [];

if ($resMaquinas) {
    while ($maq = $resMaquinas->fetch_assoc()) {
        $statusInfo = calcularStatusDashboard($conn, $maq['id']);
        if ($statusInfo['status'] == 'VENCIDO') {
            $maq['vencimento'] = $statusInfo['data'];
            $preventivasVencidas[] = $maq;
        } elseif ($statusInfo['status'] == 'PROXIMO') {
            $maq['vencimento'] = $statusInfo['data'];
            $preventivasProximas[] = $maq;
        }
    }
}

// Contadores Gerais
$count_maquinas = $conn->query("SELECT COUNT(*) as total FROM maquinas")->fetch_assoc()['total'];
$count_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
$count_os_abertas = $conn->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status IN ('Em Aberto', 'Aguardando Aprovação')")->fetch_assoc()['total'];
$count_os_andamento = $conn->query("SELECT COUNT(*) as total FROM ordens_servico WHERE status = 'Aceita'")->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">

    <style>
        .card-box {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            width: 90%;
            margin-bottom: 30px;
        }

        .card {
            flex: none;
            width: 100%;
            min-height: 120px;
            padding: 15px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-top: 4px solid var(--corBase);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            border-bottom: none;
            padding: 0;
            opacity: 0.8;
        }

        .card-header p {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
        }

        .card-info h3 {
            font-size: 2.2rem;
            font-weight: 800;
            margin: 5px 0 0 0;
            color: var(--corBase);
        }

        .div-dad {
            width: 90%;
            background: var(--corFundo2);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--sombra);
            border: 1px solid var(--corBordas);
            margin-bottom: 25px;
        }

        .div-dad h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--corBordas);
            color: var(--corTxt3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .div-dad-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .div-row {
            display: grid;
            grid-template-columns: 5px 1fr 2fr 1.5fr 1fr;
            gap: 15px;
            align-items: center;
            padding: 12px 15px;
            background: var(--corFundo);
            border-radius: 8px;
            transition: 0.2s;
            color: var(--corTxt3);
            border: 1px solid transparent;
        }

        .div-row:hover {
            border-color: var(--corBordas);
            background: var(--hoverTr);
        }

        .status-indicator {
            height: 30px;
            width: 5px;
            border-radius: 5px;
        }

        .status-danger { background: var(--status-danger); }
        .status-warning { background: var(--status-warning); }
        .status-success { background: var(--status-ok); }

        .div-items p {
            font-size: 0.65rem;
            opacity: 0.6;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .div-items div:last-child {
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .empty-msg {
            text-align: center;
            padding: 20px;
            opacity: 0.5;
            font-style: italic;
            font-size: 0.9rem;
        }

        .badge-status {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-aberto { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
        .badge-aceita { background: rgba(40, 167, 69, 0.2); color: #28a745; }

        @media (max-width: 992px) {
            .div-row {
                grid-template-columns: 5px 1fr 1fr;
            }
            .div-items:nth-child(n+4) { display: none; }
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main" style="padding-bottom: 5%;">
        <?php require __DIR__ . '/../components/header.php'; ?>

        <div class="card-box">
            <div class="card" style="border-top-color: var(--corBase);">
                <div class="card-header">
                    <p>MÁQUINAS</p>
                    <i class="bi bi-gear-wide-connected" style="color: var(--corBase); opacity: 0.3; float: right;"></i>
                </div>
                <div class="card-info">
                    <h3><?= $count_maquinas ?></h3>
                </div>
            </div>
            
            <div class="card" style="border-top-color: #ffc107;">
                <div class="card-header">
                    <p>O.S. EM ABERTO</p>
                    <i class="bi bi-exclamation-circle-fill" style="color: #ffc107; opacity: 0.3; float: right;"></i>
                </div>
                <div class="card-info">
                    <h3 style="color: #ffc107;"><?= $count_os_abertas ?></h3>
                </div>
            </div>

            <div class="card" style="border-top-color: #17a2b8;">
                <div class="card-header">
                    <p>EM ANDAMENTO</p>
                    <i class="bi bi-tools" style="color: #17a2b8; opacity: 0.3; float: right;"></i>
                </div>
                <div class="card-info">
                    <h3 style="color: #17a2b8;"><?= $count_os_andamento ?></h3>
                </div>
            </div>

            <div class="card" style="border-top-color: #28a745;">
                <div class="card-header">
                    <p>VENCIDAS / PRÓX.</p>
                    <i class="bi bi-calendar-check-fill" style="color: #28a745; opacity: 0.3; float: right;"></i>
                </div>
                <div class="card-info">
                    <h3 style="color: #28a745;"><?= count($preventivasVencidas) + count($preventivasProximas) ?></h3>
                </div>
            </div>
        </div>

        <!-- Seção: Corretivas Ativas -->
        <div class="div-dad">
            <h3 style="color: var(--status-danger);">
                <i class="bi bi-wrench-adjustable"></i> Manutenções Corretivas Ativas
            </h3>
            <div class="div-dad-content">
                <?php if ($resCorretivas && $resCorretivas->num_rows > 0): ?>
                    <?php while ($os = $resCorretivas->fetch_assoc()): ?>
                        <div class="div-row">
                            <div class="status-indicator <?= $os['status'] == 'Aceita' ? 'status-success' : 'status-danger' ?>"></div>
                            <div class="div-items">
                                <p>Patrimônio</p>
                                <div><?= htmlspecialchars($os['patrimonio'] ?? 'N/A') ?></div>
                            </div>
                            <div class="div-items">
                                <p>Problema</p>
                                <div title="<?= htmlspecialchars($os['descricao']) ?>"><?= htmlspecialchars(mb_strimwidth($os['descricao'], 0, 60, "...")) ?></div>
                            </div>
                            <div class="div-items">
                                <p>Responsável</p>
                                <div><?= htmlspecialchars($os['tecnico'] ?? 'Aguardando...') ?></div>
                            </div>
                            <div class="div-items">
                                <p>Status</p>
                                <div>
                                    <span class="badge-status <?= $os['status'] == 'Aceita' ? 'badge-aceita' : 'badge-aberto' ?>">
                                        <?= htmlspecialchars($os['status']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-msg">Nenhuma manutenção corretiva ativa no momento.</p>
                <?php endif; ?>

                <div class="div-btn-adc" style="margin-top: 10px;">
                    <button class="btn" style="width: auto; height: 35px; padding: 0 20px;" onclick="window.location.href='gerencias_os.php'">Ver Todas O.S <i class="bi bi-arrow-right-short"></i></button>
                </div>
            </div>
        </div>

        <!-- Seção: Preventivas Vencidas -->
        <div class="div-dad">
            <h3 style="color: var(--status-danger);">
                <i class="bi bi-exclamation-triangle-fill"></i> Preventivas Vencidas
            </h3>
            <div class="div-dad-content">
                <?php if (count($preventivasVencidas) > 0): ?>
                    <?php foreach (array_slice($preventivasVencidas, 0, 4) as $maq): ?>
                        <div class="div-row">
                            <div class="status-indicator status-danger"></div>
                            <div class="div-items">
                                <p>Equipamento</p>
                                <div><?= htmlspecialchars($maq['denominacao']) ?></div>
                            </div>
                            <div class="div-items">
                                <p>Identificação</p>
                                <div><?= htmlspecialchars($maq['numero_identificacao']) ?></div>
                            </div>
                            <div class="div-items">
                                <p>Status</p>
                                <div style="color: var(--status-danger); font-weight: 800;">VENCIDO</div>
                            </div>
                            <div class="div-items">
                                <p>Vencimento</p>
                                <div><?= $maq['vencimento'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-msg">Nenhuma preventiva vencida! Bom trabalho.</p>
                <?php endif; ?>

                <div class="div-btn-adc" style="margin-top: 10px;">
                    <button class="btn" style="background: var(--status-danger); width: auto; height: 35px; padding: 0 20px;" onclick="window.location.href='preventiva.php'">Abrir Checklist <i class="bi bi-clipboard-check"></i></button>
                </div>
            </div>
        </div>

        <!-- Seção: Preventivas Programadas -->
        <div class="div-dad">
            <h3 style="color: var(--status-warning);">
                <i class="bi bi-clock-fill"></i> Preventivas para os próximos 7 dias
            </h3>
            <div class="div-dad-content">
                <?php if (count($preventivasProximas) > 0): ?>
                    <?php foreach (array_slice($preventivasProximas, 0, 4) as $maq): ?>
                        <div class="div-row">
                            <div class="status-indicator status-warning"></div>
                            <div class="div-items">
                                <p>Equipamento</p>
                                <div><?= htmlspecialchars($maq['denominacao']) ?></div>
                            </div>
                            <div class="div-items">
                                <p>Identificação</p>
                                <div><?= htmlspecialchars($maq['numero_identificacao']) ?></div>
                            </div>
                            <div class="div-items">
                                <p>Status</p>
                                <div style="color: var(--status-warning); font-weight: 800;">PRÓXIMO</div>
                            </div>
                            <div class="div-items">
                                <p>Vencimento</p>
                                <div><?= $maq['vencimento'] ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="empty-msg">Nenhuma manutenção programada para os próximos 7 dias.</p>
                <?php endif; ?>

                <div class="div-btn-adc" style="margin-top: 10px;">
                    <button class="btn" style="background: var(--status-warning); color: #000; width: auto; height: 35px; padding: 0 20px;" onclick="window.location.href='preventiva.php'">Abrir Preventivas <i class="bi bi-calendar-event"></i></button>
                </div>
            </div>
        </div>
    </section>

    <script src="../../js/scripts.js" defer></script>
    <?php if (isset($_SESSION['user_senha_padrao']) && $_SESSION['user_senha_padrao'] == 1) : ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('changePassword');
                if (modal) modal.style.display = 'flex';
            });
        </script>
    <?php endif; ?>
</body>

</html>