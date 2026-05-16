<?php 
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php'; 

// --- Lógica de Paginação, Busca e Filtros ---
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$filtro_marca = isset($_GET['filtro-marca']) ? trim($_GET['filtro-marca']) : '';
$filtro_setor = isset($_GET['filtro-setor']) ? trim($_GET['filtro-setor']) : '';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 8;

// Queries para filtros rápidos
$marcas_lista = [];
$r = $conn->query("SELECT DISTINCT marca FROM maquinas WHERE marca IS NOT NULL AND marca != '' ORDER BY marca ASC");
if ($r) { while ($row = $r->fetch_assoc()) $marcas_lista[] = $row['marca']; }

$setores_lista = [];
$r = $conn->query("SELECT DISTINCT setor FROM maquinas WHERE setor IS NOT NULL AND setor != '' ORDER BY setor ASC");
if ($r) { while ($row = $r->fetch_assoc()) $setores_lista[] = $row['setor']; }

// Construir WHERE
$where = "";
$conditions = [];
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn, $busca_atual);
    $conditions[] = "(denominacao LIKE '%$termo%' OR numero_identificacao LIKE '%$termo%' OR marca LIKE '%$termo%' OR modelo LIKE '%$termo%')";
}
if ($filtro_marca !== '') {
    $conditions[] = "marca = '" . mysqli_real_escape_string($conn, $filtro_marca) . "'";
}
if ($filtro_setor !== '') {
    $conditions[] = "setor = '" . mysqli_real_escape_string($conn, $filtro_setor) . "'";
}
if (!empty($conditions)) {
    $where = " WHERE " . implode(" AND ", $conditions);
}

$sql_count = "SELECT COUNT(*) as total FROM maquinas" . $where;
$res_count = $conn->query($sql_count);
$total_registros = $res_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);
if ($total_paginas == 0) $total_paginas = 1;
if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;

$offset = ($pagina_atual - 1) * $limite;

$sql = "SELECT * FROM maquinas" . $where . " ORDER BY denominacao ASC LIMIT $limite OFFSET $offset";
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção Corretiva - SENAI</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>
    <section class="sec-main">
        <?php require __DIR__ . '/../components/header.php'; ?>

        <div class="page-actions-bar">
            <form action="" method="GET" class="page-search-form">
                <div class="page-search-box <?php echo $busca_atual ? 'has-content' : ''; ?>">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar máquina...">
                    <?php if ($busca_atual): ?> <a href="?" class="page-clear-btn"><i class="bi bi-x-lg"></i></a> <?php endif; ?>
                </div>
                <button type="submit" class="btn-search"><i class="bi bi-search"></i></button>
                <?php if (!empty($marcas_lista)): ?>
                <div class="page-filter-box">
                    <label>Marca:</label>
                    <select name="filtro-marca" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <?php foreach ($marcas_lista as $m): ?>
                            <option value="<?= htmlspecialchars($m) ?>" <?= $filtro_marca === $m ? 'selected' : '' ?>><?= htmlspecialchars($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if (!empty($setores_lista)): ?>
                <div class="page-filter-box">
                    <label>Setor:</label>
                    <select name="filtro-setor" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <?php foreach ($setores_lista as $s): ?>
                            <option value="<?= htmlspecialchars($s) ?>" <?= $filtro_setor === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </form>
            <button class="btn-page-action" style="background: #198754;" onclick="exportarCSV()">
                <i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar CSV
            </button>
            <button class="btn-page-action" style="background: #6c757d;" onclick="imprimirRelatorio()">
                <i class="bi bi-printer-fill"></i> Imprimir Corretivas
            </button>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo"><i class="bi bi-wrench"></i><h2>Manutenção Corretiva</h2></div>
            <div class="tabela-wrapper">
                <table class="tabela-main">
                    <thead><tr><th>Denominação</th><th>Marca/Modelo</th><th>NI</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php while ($linha = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($linha["denominacao"]); ?></td>
                                <td><?php echo htmlspecialchars($linha["marca"] . " / " . $linha["modelo"]); ?></td>
                                <td><?php echo htmlspecialchars($linha["numero_identificacao"]); ?></td>
                                <td>
                                    <button class="btnAcao deletar" onclick="showModal('corretiva', <?php echo $linha['id']; ?>)">
                                        <i class="bi bi-wrench-adjustable"></i> Abrir O.S.
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($resultado->num_rows == 0): ?><tr><td colspan="4">Nenhuma máquina encontrada.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php
                $pag_params = http_build_query(array_filter([
                    'search' => $busca_atual,
                    'filtro-marca' => $filtro_marca,
                    'filtro-setor' => $filtro_setor,
                ], fn($v) => $v !== ''));
            ?>
            <div class="page-pagination">
                <?php if ($pagina_atual > 1): ?><a href="?<?= $pag_params ?>&page=<?= $pagina_atual - 1 ?>" class="pag-btn">Anterior</a><?php endif; ?>
                <span class="pag-current">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>
                <?php if ($pagina_atual < $total_paginas): ?><a href="?<?= $pag_params ?>&page=<?= $pagina_atual + 1 ?>" class="pag-btn">Próxima</a><?php endif; ?>
            </div>
        </div>
    </section>
    <script src="../../js/scripts.js" defer></script>
    <script>
        function imprimirRelatorio() {
            const params = new URLSearchParams(window.location.search);
            window.open('relatorio_print.php?tipo=corretiva&' + params.toString(), '_blank');
        }
        function exportarCSV() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '../actions/exportar_csv.php?tipo=corretiva&' + params.toString();
        }
    </script>
</body>
</html>