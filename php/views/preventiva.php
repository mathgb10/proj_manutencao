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
        /* Estilos da Pesquisa e Filtros (Isolados e modernos) */
        .preventiva-search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 20px;
        }

        .preventiva-form {
            flex: 1;
            max-width: 800px;
            display: flex;
            gap: 15px;
            height: 48px;
        }

        .preventiva-search-box {
            display: flex;
            align-items: center;
            background: rgba(40, 40, 40, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 0 15px;
            flex: 1;
            transition: 0.3s;
        }
        
        html[data-tema='claro'] .preventiva-search-box {
            background: #f8f9fa;
            border-color: #ddd;
        }

        .preventiva-search-box:focus-within {
            border-color: #777;
        }

        .preventiva-search-box i.search-icon {
            color: #888;
            font-size: 1.1rem;
            margin-right: 12px;
        }

        .preventiva-search-box input {
            border: none !important;
            background: transparent !important;
            outline: none !important;
            box-shadow: none !important;
            color: var(--corTxt3);
            font-size: 0.95rem;
            width: 100%;
            height: 100%;
            padding: 0;
            margin: 0;
        }

        .preventiva-search-box input::placeholder {
            color: #777;
        }

        .preventiva-search-box .btn-clear-search {
            color: #777;
            text-decoration: none;
            transition: 0.2s;
            margin-left: 10px;
            display: flex;
            align-items: center;
        }
        .preventiva-search-box .btn-clear-search:hover {
            color: var(--status-danger, #ff2d35);
        }

        .preventiva-select-box {
            display: flex;
            align-items: center;
            background: rgba(40, 40, 40, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 0 15px;
            gap: 10px;
            min-width: 200px;
            transition: 0.3s;
        }

        html[data-tema='claro'] .preventiva-select-box {
            background: #f8f9fa;
            border-color: #ddd;
        }

        .preventiva-select-box:focus-within {
            border-color: #777;
        }

        .preventiva-select-box label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--corTxt3);
            text-transform: uppercase;
        }

        .preventiva-select-box select {
            border: none;
            background: transparent;
            outline: none;
            color: var(--corTxt3);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            appearance: none;
            padding-right: 25px;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23fff%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right center;
            background-size: 10px auto;
        }
        
        html[data-tema='claro'] .preventiva-select-box select {
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
        }

        .preventiva-select-box select option {
            background: var(--corFundo2);
            color: var(--corTxt3);
        }

        .btn-novo-registro {
            height: 48px;
            display: inline-flex;
            align-items: center;
        }

        /* Estilos da Tabela Main */
        .tabela-main td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid var(--corBordas);
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
            color: #000;
            display: inline-block;
        }
        .bg-warning { background: var(--status-warning); }
        .bg-danger { background: var(--status-danger); color: white; }
        .bg-success { background: var(--status-ok); }
        
        .text-warning { color: var(--status-warning); }
        .text-danger { color: var(--status-danger); }
        .text-success { color: var(--status-ok); }

        /* Paginação Moderna Estilo Referência */
        .preventiva-paginacao {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
            padding: 10px 0;
        }

        .btn-ant-novo {
            color: #777;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        .btn-ant-novo:not(.disabled):hover {
            color: var(--status-danger, #ff2d35);
        }
        .btn-ant-novo.disabled {
            color: #444;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .pagina-atual-texto {
            background: #1e3a8a; /* Azul sofisticado escuro */
            color: #60a5fa; /* Azul clarinho pro texto */
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 0.9rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        html[data-tema='claro'] .pagina-atual-texto {
            background: #e0f2fe;
            color: #0369a1;
        }

        .btn-prox-novo {
            color: #777;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        .btn-prox-novo:not(.disabled):hover {
            color: var(--status-danger, #ff2d35);
        }
        .btn-prox-novo.disabled {
            color: #444;
            cursor: not-allowed;
            opacity: 0.8;
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

        <!-- Botões e Pesquisa com Classes Únicas -->
        <div class="preventiva-search-header">
            <form action="" method="GET" class="preventiva-form">
                <div class="preventiva-search-box">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" name="search" id="pesquisa"
                        value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar NI ou Nome..."
                        class="preventiva-input">
                    <?php if ($busca_atual): ?>
                        <a href="?filtro-status=<?php echo urlencode($status_atual); ?>" class="btn-clear-search"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>

                <div class="preventiva-select-box">
                    <label>STATUS:</label>
                    <select name="filtro-status" onchange="this.form.submit()">
                        <option value="todos" <?php echo $status_atual == 'todos' ? 'selected' : ''; ?>>Todos</option>
                        <option value="ok" <?php echo $status_atual == 'ok' ? 'selected' : ''; ?>>Em Dia</option>
                        <option value="proximos" <?php echo $status_atual == 'proximos' ? 'selected' : ''; ?>>Próximos / Pendentes</option>
                        <option value="vencidos" <?php echo $status_atual == 'vencidos' ? 'selected' : ''; ?>>Vencidos</option>
                    </select>
                </div>
            </form>
            <button class="btn btn-novo-registro" onclick="showModal('preventiva')">Novo Registro <i class="bi bi-plus-circle"></i></button>
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

            <!-- Paginação Moderna -->
            <div class="preventiva-paginacao">
                <?php if ($pagina_atual > 1): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&filtro-status=<?php echo urlencode($status_atual); ?>&page=<?php echo $pagina_atual - 1; ?>" class="btn-ant-novo"><i class="bi bi-chevron-left"></i> Anterior</a>
                <?php else: ?>
                    <span class="btn-ant-novo disabled"><i class="bi bi-chevron-left"></i> Anterior</span>
                <?php endif; ?>

                <span class="pagina-atual-texto">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&filtro-status=<?php echo urlencode($status_atual); ?>&page=<?php echo $pagina_atual + 1; ?>" class="btn-prox-novo">Próxima <i class="bi bi-chevron-right"></i></a>
                <?php else: ?>
                    <span class="btn-prox-novo disabled">Próxima <i class="bi bi-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <!-- Scripts de Funcionalidade -->
    <script src="../../js/scripts.js"></script>
    <script>
        // Funções para manter o modal abrindo
        function openChecklist(nome, id) {
            if (typeof window.openChecklist === 'function') {
                window.openChecklist(nome, id);
            }
        }
        function openHistory(nome, id) {
            if (typeof window.openHistory === 'function') {
                window.openHistory(nome, id);
            }
        }
    </script>
</body>

</html>
