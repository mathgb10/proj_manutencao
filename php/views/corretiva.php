<?php 
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php'; 

// --- Lógica de Paginação e Busca (Padrão Preventiva) ---
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 8; // Mostrar 8 por página

// 1. Contar total de registros com filtro
$sql_count = "SELECT COUNT(*) as total FROM maquinas";
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn, $busca_atual);
    $sql_count .= " WHERE denominacao LIKE '%$termo%' OR numero_identificacao LIKE '%$termo%' OR marca LIKE '%$termo%' OR modelo LIKE '%$termo%'";
}
$res_count = $conn->query($sql_count);
$total_registros = $res_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);
if ($total_paginas == 0) $total_paginas = 1;
if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;

$offset = ($pagina_atual - 1) * $limite;

// 2. Buscar registros paginados
$sql = "SELECT * FROM maquinas";
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn, $busca_atual);
    $sql .= " WHERE denominacao LIKE '%$termo%' OR numero_identificacao LIKE '%$termo%' OR marca LIKE '%$termo%' OR modelo LIKE '%$termo%'";
}
$sql .= " ORDER BY denominacao ASC LIMIT $limite OFFSET $offset";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Máquinas - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
    <script src="../../js/scripts.js" defer></script>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

        <!-- Barra de Ações Unificada -->
        <div class="page-actions-bar">
            <form action="" method="GET" class="page-search-form">
                <div class="page-search-box <?php echo $busca_atual ? 'has-content' : ''; ?>">
                    <input type="text" name="search" id="pesquisa"
                        value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar denominação, marca ou NI...">
                    <?php if ($busca_atual): ?>
                        <a href="?" class="page-clear-btn"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <div class="tabela-bg2" id="tabe">
            <div class="tabela-titulo">
                <i class="bi bi-wrench"></i>
                <h2>Corretiva</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main">
                    <thead>
                        <th>Denominação</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>N° Identificação</th>
                        <th>Ações</th>
                    </thead>
                    <tbody id="tabela-corretiva">
                        <?php
                        if ($resultado && $resultado->num_rows > 0) {
                            while ($linha = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($linha["denominacao"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["marca"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["modelo"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["numero_identificacao"]) . "</td>";
                                echo "<td>
                                    <div style='display: flex; gap: 5px; justify-content: center;'>
                                        <button class='btnAcao deletar' type='button' title='Abrir Corretiva' onclick=\"showModal('corretiva'," . $linha['id'] . ")\"><i class='bi bi-wrench-adjustable'></i> Abrir O.S.</button>
                                    </div>
                                  </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:30px; color: #888;'>Nenhuma máquina encontrada para esta pesquisa.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação Unificada -->
            <div class="page-pagination">
                <?php if ($pagina_atual > 1): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&page=<?php echo $pagina_atual - 1; ?>" class="pag-btn"><i class="bi bi-chevron-left"></i> Anterior</a>
                <?php else: ?>
                    <span class="pag-btn disabled"><i class="bi bi-chevron-left"></i> Anterior</span>
                <?php endif; ?>

                <span class="pag-current">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&page=<?php echo $pagina_atual + 1; ?>" class="pag-btn">Próxima <i class="bi bi-chevron-right"></i></a>
                <?php else: ?>
                    <span class="pag-btn disabled">Próxima <i class="bi bi-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>