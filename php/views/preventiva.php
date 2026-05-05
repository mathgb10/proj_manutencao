<?php
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php';

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

// Buscar máquinas (todas para permitir live search no front-end)
$sql = "SELECT * FROM maquinas";
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
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

        <!-- Botões e Pesquisa -->
        <div class="div-btns-pages">
            <form action="" method="GET" class="form-pesquisa" onsubmit="event.preventDefault();">
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

                    <div class="filtrar-status">
                        <label for="select-filtro-preventiva">Status:</label>
                        <select id="select-filtro-preventiva" name="filtro-status" onchange="filtrarPreventiva()">
                            <option value="todos">Todos</option>
                            <option value="ok">Em Dia</option>
                            <option value="proximos">Próximos</option>
                            <option value="vencidos">Vencidos</option>
                        </select>
                    </div>

                    <!-- Hidden submit button to allow Enter to search -->
                    <button type="submit" style="display: none;"></button>
                </div>
            </form>
            <button class="btn" onclick="showModal('preventiva')">Novo Registro <i class="bi bi-plus-circle"></i></button>
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
                                    <td><?php echo htmlspecialchars($maquina['numero_identificacao']); ?></td>
                                    <td><?php echo htmlspecialchars($maquina['denominacao']); ?> <small
                                            style="color: #888;">(<?php echo htmlspecialchars($maquina['modelo']); ?>)</small></td>
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
</body>

</html>
