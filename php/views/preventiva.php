<?php
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php';

// --- Lógica de Data e Status (Simplificada e Otimizada) ---
function calcularStatusArray($lastDate, $intervalo)
{
    if (!$intervalo) $intervalo = 30;

    // Se nunca houve manutenção
    if (!$lastDate) {
        return ['status' => 'PENDENTE', 'class' => 'warning', 'label' => 'PENDENTE', 'data' => 'S/ REGISTRO'];
    }

    $proxima = date('Y-m-d', strtotime($lastDate . " + $intervalo days"));
    $hoje = date('Y-m-d');
    $seteDias = date('Y-m-d', strtotime('+7 days'));

    if ($proxima < $hoje) {
        return ['status' => 'VENCIDO', 'class' => 'danger', 'label' => 'VENCIDO', 'data' => date('d/m/Y', strtotime($proxima))];
    } elseif ($proxima <= $seteDias) {
        return ['status' => 'PROXIMO', 'class' => 'warning', 'label' => 'PRÓXIMO', 'data' => date('d/m/Y', strtotime($proxima))];
    } else {
        return ['status' => 'OK', 'class' => 'success', 'label' => 'EM DIA', 'data' => date('d/m/Y', strtotime($proxima))];
    }
}

// Pegar filtros e paginação
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_atual = isset($_GET['filtro-status']) ? $_GET['filtro-status'] : 'todos';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 8; // Mostrar 8 por página

// Buscar máquinas (Query Otimizada evitando N+1)
$sql = "
    SELECT 
        m.*, 
        (SELECT MAX(data_realizada) FROM historico_manutencao WHERE maquina_id = m.id) as ultima,
        (SELECT MIN(
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
        ) FROM checklist_itens WHERE maquina_id = m.id) as intervalo
    FROM maquinas m
    ORDER BY m.denominacao ASC
";
$resMaquinas = $conn->query($sql);

$maquinas_filtradas = [];

if ($resMaquinas && $resMaquinas->num_rows > 0) {
    while ($maquina = $resMaquinas->fetch_assoc()) {
        $dataStatus = calcularStatusArray($maquina['ultima'], $maquina['intervalo']);
        
        $filterStatus = strtolower($dataStatus['status']);
        if ($filterStatus == 'pendente' || $filterStatus == 'proximo') $filterStatus = 'proximos';
        if ($filterStatus == 'vencido') $filterStatus = 'vencidos';

        // Lógica de Filtro: Status
        if ($status_atual != 'todos' && $status_atual != $filterStatus) {
            continue;
        }

        // Lógica de Filtro: Busca
        if ($busca_atual != '') {
            $termo = strtolower($busca_atual);
            $nome = strtolower($maquina['denominacao']);
            $ni = strtolower($maquina['numero_identificacao']);
            if (strpos($nome, $termo) === false && strpos($ni, $termo) === false) {
                continue;
            }
        }
        
        $maquina['dataStatus'] = $dataStatus;
        $maquinas_filtradas[] = $maquina;
    }
}

// Configurar Paginação Baseado nos Filtros
$total_registros = count($maquinas_filtradas);
$total_paginas = ceil($total_registros / $limite);
if ($total_paginas == 0) $total_paginas = 1;
if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;

$offset = ($pagina_atual - 1) * $limite;
$maquinas_paginadas = array_slice($maquinas_filtradas, $offset, $limite);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão Preventiva - SENAI</title>

    <!-- Estilos Globais -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/global.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
    
    <style>
        /* Estilos específicos da Preventiva — badge de status e botões de ação */
        .tabela-main td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid var(--corBordas);
            color: var(--corTxt3);
        }
        .btnAcao {
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            color: white;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .btnAcao.checklist { background: var(--status-pereira, #4f4fcf); }
        .btnAcao.checklist:hover { filter: brightness(1.2); }
        .btnAcao.history { background: #6c757d; }
        .btnAcao.history:hover { background: #5a6268; }

        .badge-status {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.75rem;
            display: inline-block;
        }
        .bg-warning { background: var(--status-warning); color: #000; }
        .bg-danger { background: var(--status-danger); color: #fff; }
        .bg-success { background: var(--status-ok); color: #000; }

        .text-warning { color: var(--status-warning); }
        .text-danger { color: var(--status-danger); }
        .text-success { color: var(--status-ok); }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

        <!-- Barra de Ações — Padrão Unificado do Sistema -->
        <div class="page-actions-bar">
            <form action="" method="GET" class="page-search-form">
                <div class="page-search-box <?php echo $busca_atual ? 'has-content' : ''; ?>">
                    <input type="text" name="search" id="pesquisa"
                        value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar NI ou Nome...">
                    <?php if ($busca_atual): ?>
                        <a href="?filtro-status=<?php echo urlencode($status_atual); ?>" class="page-clear-btn"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>

                <div class="page-filter-box">
                    <label for="filtro-status-prev">Status:</label>
                    <select id="filtro-status-prev" name="filtro-status" onchange="this.form.submit()">
                        <option value="todos" <?php echo $status_atual == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="ok" <?php echo $status_atual == 'ok' ? 'selected' : ''; ?>>Em Dia</option>
                        <option value="proximos" <?php echo $status_atual == 'proximos' ? 'selected' : ''; ?>>Próximos / Pendentes</option>
                        <option value="vencidos" <?php echo $status_atual == 'vencidos' ? 'selected' : ''; ?>>Vencidos</option>
                    </select>
                </div>
            </form>
            <button class="btn-page-action" onclick="showModal('preventiva')">
                <i class="bi bi-plus-circle"></i> Novo Registro
            </button>
        </div>

        <div class="tabela-bg2" id="tabe">
            <div class="tabela-titulo">
                <i class="bi bi-shield-check"></i>
                <h2>Preventivas</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main" id="mainTable">
                    <thead>
                        <tr>
                            <th>NI</th>
                            <th>Denominação</th>
                            <th>Status</th>
                            <th>Próxima Preventiva</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-maquinas-body">
                        <?php
                        if (count($maquinas_paginadas) > 0) {
                            foreach ($maquinas_paginadas as $maquina) {
                                $dataStatus = $maquina['dataStatus'];
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($maquina['numero_identificacao']); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($maquina['denominacao']); ?></strong>
                                        <?php 
                                        $modelo = trim($maquina['modelo']);
                                        $denom = trim($maquina['denominacao']);
                                        if (!empty($modelo) && strcasecmp($modelo, $denom) !== 0) {
                                            echo '<br><small style="color: var(--corTxt2); opacity: 0.7;">(' . htmlspecialchars($modelo) . ')</small>';
                                        }
                                        ?>
                                    </td>
                                    <td><span class="badge-status bg-<?php echo $dataStatus['class']; ?>"><?php echo $dataStatus['label']; ?></span></td>
                                    <td><b class="text-<?php echo $dataStatus['class']; ?>"><?php echo $dataStatus['data']; ?></b></td>
                                    <td>
                                        <div style="display: flex; gap: 8px; justify-content: center;">
                                            <button class="btnAcao checklist" title="Abrir Checklist"
                                                onclick="openChecklist('<?php echo addslashes($maquina['denominacao']); ?>', <?php echo $maquina['id']; ?>)">
                                                <i class="bi bi-list-check"></i> Abrir Checklist
                                            </button>
                                            <button class="btnAcao history" title="Ver Histórico"
                                                onclick="openHistory('<?php echo addslashes($maquina['denominacao']); ?>', <?php echo $maquina['id']; ?>)">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Nenhuma preventiva encontrada com estes filtros.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação Unificada -->
            <div class="page-pagination">
                <?php if ($pagina_atual > 1): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&filtro-status=<?php echo urlencode($status_atual); ?>&page=<?php echo $pagina_atual - 1; ?>" class="pag-btn"><i class="bi bi-chevron-left"></i> Anterior</a>
                <?php else: ?>
                    <span class="pag-btn disabled"><i class="bi bi-chevron-left"></i> Anterior</span>
                <?php endif; ?>

                <span class="pag-current">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&filtro-status=<?php echo urlencode($status_atual); ?>&page=<?php echo $pagina_atual + 1; ?>" class="pag-btn">Próxima <i class="bi bi-chevron-right"></i></a>
                <?php else: ?>
                    <span class="pag-btn disabled">Próxima <i class="bi bi-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <!-- Scripts de Funcionalidade -->
    <script src="../../js/scripts.js"></script>
</body>

</html>
