<?php
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php';

// --- Lógica de Data e Status ---
function calcularStatusArray($lastDate, $intervalo)
{
    if (!$intervalo) $intervalo = 30;

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
$limite = 8;

// Buscar máquinas
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

        if ($status_atual != 'todos' && $status_atual != $filterStatus) {
            continue;
        }

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
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        .tabela-main td { padding: 15px; text-align: center; border-bottom: 1px solid var(--corBordas); color: var(--corTxt3); }
        .btnAcao { padding: 6px 12px; border-radius: 4px; border: none; color: white; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px; font-size: 0.85rem; font-weight: 600; }
        .btnAcao.checklist { background: var(--status-pereira, #4f4fcf); }
        .btnAcao.checklist:hover { filter: brightness(1.2); }
        .btnAcao.history { background: #6c757d; }
        .btnAcao.history:hover { background: #5a6268; }
        .badge-status { padding: 6px 12px; border-radius: 6px; font-weight: 800; font-size: 0.75rem; display: inline-block; }
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
        <?php require __DIR__ . '/../components/header.php'; ?>
        <div class="page-actions-bar">
            <form action="" method="GET" class="page-search-form">
                <div class="page-search-box <?php echo $busca_atual ? 'has-content' : ''; ?>">
                    <input type="text" name="search" id="pesquisa" value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar NI ou Nome...">
                    <?php if ($busca_atual): ?> <a href="?filtro-status=<?php echo urlencode($status_atual); ?>" class="page-clear-btn"><i class="bi bi-x-lg"></i></a> <?php endif; ?>
                </div>
                <button type="submit" class="btn-search"><i class="bi bi-search"></i></button>
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
            <button class="btn-page-action" onclick="showModal('preventiva')"><i class="bi bi-plus-circle"></i> Novo Registro</button>
            <button class="btn-page-action" style="background: #198754;" onclick="exportarCSV()"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar CSV</button>
            <button class="btn-page-action" style="background: #6c757d;" onclick="imprimirRelatorio()"><i class="bi bi-printer-fill"></i> Imprimir</button>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo"><i class="bi bi-shield-check"></i><h2>Preventivas</h2></div>
            <div class="tabela-wrapper">
                <table class="tabela-main">
                    <thead><tr><th>NI</th><th>Denominação</th><th>Status</th><th>Próxima Preventiva</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($maquinas_paginadas as $maquina): $dataStatus = $maquina['dataStatus']; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($maquina['numero_identificacao']); ?></td>
                                <td><strong><?php echo htmlspecialchars($maquina['denominacao']); ?></strong></td>
                                <td><span class="badge-status bg-<?php echo $dataStatus['class']; ?>"><?php echo $dataStatus['label']; ?></span></td>
                                <td><b class="text-<?php echo $dataStatus['class']; ?>"><?php echo $dataStatus['data']; ?></b></td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <button class="btnAcao checklist" onclick="openChecklist('<?php echo addslashes($maquina['denominacao']); ?>', <?php echo $maquina['id']; ?>)"><i class="bi bi-list-check"></i> Checklist</button>
                                        <button class="btnAcao history" onclick="openHistory('<?php echo addslashes($maquina['denominacao']); ?>', <?php echo $maquina['id']; ?>)"><i class="bi bi-clock-history"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($maquinas_paginadas)): ?><tr><td colspan="5">Nenhuma preventiva encontrada.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="page-pagination">
                <?php if ($pagina_atual > 1): ?><a href="?search=<?php echo urlencode($busca_atual); ?>&filtro-status=<?php echo urlencode($status_atual); ?>&page=<?php echo $pagina_atual - 1; ?>" class="pag-btn">Anterior</a><?php endif; ?>
                <span class="pag-current">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>
                <?php if ($pagina_atual < $total_paginas): ?><a href="?search=<?php echo urlencode($busca_atual); ?>&filtro-status=<?php echo urlencode($status_atual); ?>&page=<?php echo $pagina_atual + 1; ?>" class="pag-btn">Próxima</a><?php endif; ?>
            </div>
        </div>
    </section>
    <script src="../../js/scripts.js" defer></script>
    <script>
        function imprimirRelatorio() {
            const params = new URLSearchParams(window.location.search);
            window.open('relatorio_print.php?tipo=preventiva&' + params.toString(), '_blank');
        }
        function exportarCSV() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '../actions/exportar_csv.php?tipo=preventiva&' + params.toString();
        }
    </script>
</body>
</html>
