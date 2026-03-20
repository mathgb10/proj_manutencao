<?php
require '../controllers/validar_acesso.php';
require '../configs/conexao.php';
require '../components/modals/all_modals.php';

// --- Lógica de Data e Status (Simplificada) ---
function calcularStatus($conn, $maquina_id)
{
    // 1. Buscar a última preventiva realizada
    $sqlInitial = "SELECT MAX(data_realizada) as ultima FROM historico_manutencao WHERE maquina_id = $maquina_id";
    $result = $conn->query($sqlInitial);
    $lastDate = null;
    if ($result && $row = $result->fetch_assoc()) {
        $lastDate = $row['ultima'];
    }

    // Se nunca houve manutenção
    if (!$lastDate) {
        return ['status' => 'PENDENTE', 'class' => 'warning', 'label' => 'PENDENTE', 'data' => 'S/ REGISTRO'];
    }

    // Lógica simples: data + 30 dias (exemplo mensal)
    $proxima = date('Y-m-d', strtotime($lastDate . ' + 30 days'));
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

// Buscar máquinas
$search = $_GET['search'] ?? '';
$where = "";
if ($search) {
    $s = $conn->real_escape_string($search);
    $where = "WHERE denominacao LIKE '%$s%' OR numero_identificacao LIKE '%$s%'";
}
$sql = "SELECT * FROM maquinas $where";
$resMaquinas = $conn->query($sql);

$contadores = ['ok' => 0, 'proximos' => 0, 'vencidos' => 0, 'total' => 0];
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
    <script src="../../js/scripts.js" defer></script>
    <script src="../../js/preventiva.js" defer></script>
</head>

<body>
    <?php require '../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require '../components/header.php'; ?>

        <!-- Dashboard (Inserido conforme solicitado, Única diferença para maquinas.php) -->
        <div class="row dashboard-row" style="margin: 20px 0;">
            <div class="col-md-3">
                <div class="card-dash card-ok" onclick="filtrarListaPrincipal('ok')">
                    <small>Equipamentos em Dia</small>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 id="count-ok">0</h2>
                        <i class="bi bi-check-circle-fill text-success" style="color: var(--status-ok);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-dash card-warning" onclick="filtrarListaPrincipal('proximos')">
                    <small>Próximos (7 dias)</small>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 id="count-proximos">0</h2>
                        <i class="bi bi-clock-history text-warning" style="color: var(--status-warning);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-dash card-danger" onclick="filtrarListaPrincipal('vencidos')">
                    <small>Vencidos / Atrasados</small>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 id="count-vencidos">0</h2>
                        <i class="bi bi-exclamation-triangle-fill text-danger" style="color: var(--status-danger);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-dash card-pereira"
                    onclick="filtrarListaPrincipal('todos')">
                    <small>Total de Ativos</small>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 id="count-total">0</h2>
                        <i class="bi bi-gear-wide-connected text-primary" style="color: var(--status-pereira);"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões e Pesquisa (Igual estrutura maquinas.php) -->
        <div class="div-btns-pages">

            <form action="" method="GET" class="form-pesquisa">
                <div class="search-container">
                    <?php
                    $busca_atual = isset($_GET['search']) ? $_GET['search'] : '';
                    ?>
                    <div class="box-pesquisa">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" id="pesquisa"
                            value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar..."
                            class="input-pesquisa">
                        <?php if ($busca_atual): ?>
                            <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn-clear-search"><i
                                    class="bi bi-x-lg"></i></a>
                        <?php endif; ?>
                    </div>
                    <!-- Hidden submit button to allow Enter to search -->
                    <button type="submit" style="display: none;"></button>
                </div>
            </form>
        </div>

        <!-- Tabela Padronizada -->
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
                    <tbody>
                        <?php
                        if ($resMaquinas && $resMaquinas->num_rows > 0) {
                            while ($maquina = $resMaquinas->fetch_assoc()) {
                                $dataStatus = calcularStatus($conn, $maquina['id']);

                                // Incrementa contadores
                                if ($dataStatus['status'] == 'OK')
                                    $contadores['ok']++;
                                if ($dataStatus['status'] == 'PROXIMO' || $dataStatus['status'] == 'PENDENTE')
                                    $contadores['proximos']++;
                                if ($dataStatus['status'] == 'VENCIDO')
                                    $contadores['vencidos']++;
                                $contadores['total']++;

                                // Mapear status para classe CSS e filtro
                                $filterStatus = strtolower($dataStatus['status']);
                                if ($filterStatus == 'pendente')
                                    $filterStatus = 'proximos';
                                if ($filterStatus == 'proximo')
                                    $filterStatus = 'proximos';
                                if ($filterStatus == 'vencido')
                                    $filterStatus = 'vencidos';

                                ?>
                                <tr data-status="<?php echo $filterStatus; ?>">
                                    <td><?php echo $maquina['numero_identificacao']; ?></td>
                                    <td><?php echo $maquina['denominacao']; ?> <small
                                            style="color: #888;">(<?php echo $maquina['modelo']; ?>)</small></td>
                                    <td><span
                                            class="badge-status bg-<?php echo $dataStatus['class']; ?>"><?php echo $dataStatus['label']; ?></span>
                                    </td>
                                    <td><b
                                            class="text-<?php echo $dataStatus['class']; ?>"><?php echo $dataStatus['data']; ?></b>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 5px; justify-content: center;">
                                            <button class="btnAcao checklist" title="Abrir Checklist"
                                                onclick="openChecklist('<?php echo addslashes($maquina['denominacao']); ?>', <?php echo $maquina['id']; ?>)">
                                                <i class="bi bi-list-check"></i> Abrir Checklist
                                            </button>
                                            <button class="btnAcao history" title="Ver Histórico"
                                                style="background-color: #6c757d;"
                                                onclick="openHistory('<?php echo addslashes($maquina['denominacao']); ?>', <?php echo $maquina['id']; ?>)">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>Nenhuma máquina encontrada.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="div-btns-change">
                <button id="btn-ant" type="button"><i class="bi bi-chevron-left"></i></button>
                <button id="btn-prox" type="button"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>

    </section>

    <!-- Scripts de Funcionalidade -->
    <script src="../../js/scripts.js"></script>
    <script src="../../js/preventiva.js"></script>

    <script>
        // Atualizar contadores na tela com PHP values
        document.getElementById('count-ok').innerText = "<?php echo $contadores['ok']; ?>";
        document.getElementById('count-proximos').innerText = "<?php echo $contadores['proximos']; ?>";
        document.getElementById('count-vencidos').innerText = "<?php echo $contadores['vencidos']; ?>";
        document.getElementById('count-total').innerText = "<?php echo $contadores['total']; ?>";
    </script>
</body>

</html>