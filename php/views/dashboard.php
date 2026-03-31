<?php require __DIR__ . '\..\controllers\validar_acesso.php'; ?>
<?php require_once __DIR__ . "\..\configs\conexao.php"; ?>
<?php require __DIR__ . '\..\components\modals\all_modals.php'; ?>

<?php
// --- Lógica de Notificações ---

if (!function_exists('calcularStatusDashboard')) {
    function calcularStatusDashboard($conn, $maquina_id)
    {
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

        $proxima = date('Y-m-d', strtotime($lastDate . ' + 30 days'));
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

// Buscar Corretivas Ativas
$sqlCorretivas = "SELECT os.*, u.nome as tecnico 
                  FROM ordens_servico os 
                  LEFT JOIN usuarios u ON os.responsavel_id = u.id 
                  WHERE os.tipo = 'Corretivo' AND os.status != 'Arquivada' 
                  ORDER BY os.criado_em DESC LIMIT 3";
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
        /* Ajustes de Responsividade e Estilo do Dashboard */
        .card-box {
            display: flex;
            gap: 10px;
            width: 90%;
            height: auto !important;
        }

        .card {
            flex: 1;
            height: auto !important;
            min-height: 140px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .div-dad {
            width: 90%;
            background: var(--corFundo2);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--sombra);
            border: 1px solid var(--corBordas);
        }

        .div-dad h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--corBordas);
            color: var(--corTxt3);
        }

        .div-dad-content {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .div-row {
            display: flex;
            gap: 15px;
            align-items: center;
            padding: 15px;
            background: var(--corFundo);
            border-radius: 8px;
            transition: 0.3s;
            color: var(--corTxt3);
            width: 100%;
        }

        .div-row:hover {
            transform: translateX(5px);
            background: var(--hoverTr);
        }

        .status-indicator {
            height: 100%;
            width: 5px;
            border-radius: 5px;
        }

        .status-danger {
            background: var(--status-danger);
        }

        .status-warning {
            background: var(--status-warning);
        }

        .status-success {
            background: var(--status-ok);
        }

        .div-items p {
            font-size: 10px;
            opacity: 0.5;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .div-items div:last-child {
            font-weight: 600;
            font-size: 0.9rem;
            word-break: break-word;
        }

        .empty-msg {
            text-align: center;
            padding: 30px;
            opacity: 0.6;
            font-style: italic;
            color: var(--corTxt3);
        }

        .div-btn-adc {
            width: 100%;
            margin-top: 15px;
            display: flex;
            justify-content: flex-start;
        }

        @media (max-width: 992px) {
            .div-row {
                grid-template-columns: 5px 1fr 1fr;
                gap: 10px;
            }
        }

        @media (max-width: 600px) {
            .div-row {
                grid-template-columns: 5px 1fr;
            }

            .card-box {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main" style="padding-bottom: 5%;">
        <?php require __DIR__ . '/../components/header.php'; ?>

        <div class="card-box">
            <?php
            $count_maquinas = $conn->query("SELECT COUNT(*) as total FROM maquinas")->fetch_assoc()['total'];
            $count_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
            $check_acessorios = $conn->query("SHOW TABLES LIKE 'acessorios'");
            $count_acessorios = ($check_acessorios && $check_acessorios->num_rows > 0) ?
                $conn->query("SELECT COUNT(*) as total FROM acessorios")->fetch_assoc()['total'] : 0;
            ?>
            <div class="card card-green">
                <div class="card-header">
                    <p>Máquinas</p>
                    <i class="bi bi-gear-wide-connected"></i>
                </div>
                <div class="card-info">
                    <h3><?= $count_maquinas ?></h3>
                </div>
            </div>
            <div class="card card-green">
                <div class="card-header">
                    <p>Usuários</p>
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="card-info">
                    <h3><?= $count_usuarios ?></h3>
                </div>
            </div>
            <div class="card card-green">
                <div class="card-header">
                    <p>Acessórios</p>
                    <i class="bi bi-tools"></i>
                </div>
                <div class="card-info">
                    <h3><?= $count_acessorios ?></h3>
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
                            <div class="status-indicator status-danger"></div>
                            <div class="div-items">
                                <p>Patrimônio</p>
                                <div><?= htmlspecialchars($os['patrimonio'] ?? 'N/A') ?></div>
                            </div>
                            <div class="div-items">
                                <p>Problema</p>
                                <div><?= htmlspecialchars(mb_strimwidth($os['descricao'], 0, 40, "...")) ?></div>
                            </div>
                            <div class="div-items">
                                <p>Técnico</p>
                                <div><?= htmlspecialchars($os['tecnico'] ?? 'Não atribuído') ?></div>
                            </div>
                            <div class="div-items">
                                <p>Data</p>
                                <div><?= date('d/m H:i', strtotime($os['criado_em'])) ?></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-msg">Nenhuma manutenção corretiva ativa no momento.</p>
                <?php endif; ?>

                <div class="div-btn-adc">
                    <button class="btn" onclick="window.location.href='gerencias_os.php'">Ver Todas O.S <i class="bi bi-arrow-right-short"></i></button>
                </div>
            </div>
        </div>

        <!-- Seção: Preventivas Ativas (Vencidas) -->
        <div class="div-dad">
            <h3 style="color: var(--status-danger);">
                <i class="bi bi-exclamation-triangle-fill"></i> Preventivas Vencidas
            </h3>
            <div class="div-dad-content">
                <?php if (count($preventivasVencidas) > 0): ?>
                    <?php foreach (array_slice($preventivasVencidas, 0, 3) as $maq): ?>
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
                                <div style="color: var(--status-danger)">VENCIDO</div>
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

                <div class="div-btn-adc">
                    <button class="btn" style="background: var(--status-danger)" onclick="window.location.href='preventiva.php'">Abrir Checklist <i class="bi bi-clipboard-check"></i></button>
                </div>
            </div>
        </div>

        <!-- Seção: Preventivas Programadas (Próximas) -->
        <div class="div-dad">
            <h3 style="color: var(--status-warning);">
                <i class="bi bi-clock-fill"></i> Preventivas Programadas (Próximos 7 dias)
            </h3>
            <div class="div-dad-content">
                <?php if (count($preventivasProximas) > 0): ?>
                    <?php foreach (array_slice($preventivasProximas, 0, 3) as $maq): ?>
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
                                <div style="color: var(--status-warning)">PRÓXIMO</div>
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

                <div class="div-btn-adc">
                    <button class="btn" style="background: var(--status-warning); color: #000;" onclick="window.location.href='preventiva.php'">Abrir Preventivas <i class="bi bi-calendar-event"></i></button>
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