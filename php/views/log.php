<?php 
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php'; 

// --- Lógica de Paginação e Busca (Padrão Unificado) ---
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 10; // Logs podem mostrar um pouco mais

// 1. Contar total de registros com filtro
$sql_count = "SELECT COUNT(*) as total FROM logs 
              LEFT JOIN usuarios ON logs.usuario_id = usuarios.id";
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn, $busca_atual);
    $sql_count .= " WHERE usuarios.nome LIKE '%$termo%' OR logs.ip_address LIKE '%$termo%' OR logs.sql_command LIKE '%$termo%'";
}
$res_count = $conn->query($sql_count);
$total_registros = $res_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);
if ($total_paginas == 0) $total_paginas = 1;
if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;

$offset = ($pagina_atual - 1) * $limite;

// 2. Buscar registros paginados
$sql = "SELECT logs.*, usuarios.nome AS nome_real 
        FROM logs 
        LEFT JOIN usuarios ON logs.usuario_id = usuarios.id";
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn, $busca_atual);
    $sql .= " WHERE usuarios.nome LIKE '%$termo%' OR logs.ip_address LIKE '%$termo%' OR logs.sql_command LIKE '%$termo%'";
}
$sql .= " ORDER BY logs.id DESC LIMIT $limite OFFSET $offset";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Logs - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
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
                    <input type="text" name="search" id="pesquisa" value="<?php echo htmlspecialchars($busca_atual); ?>"
                        placeholder="Pesquisar descrição ou arquivo...">
                    <?php if ($busca_atual): ?>
                        <a href="?" class="page-clear-btn"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo">
                <i class="bi bi-file-earmark-code"></i>
                <h2>Logs do Sistema</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main">
                    <thead>
                        <th>ID</th>
                        <th>Usuário</th>
                        <th>Endereço de IP</th>
                        <th>Comando SQL</th>
                        <th>Data/Hora</th>
                    </thead>
                    <tbody id="tabela-logs">
                        <?php
                        if ($resultado && $resultado->num_rows > 0) {
                            while ($linha = $resultado->fetch_assoc()) {
                                // Verifica se encontrou o nome (se o usuário não foi excluído)
                                $nome_exibicao = !empty($linha["nome_real"]) ? htmlspecialchars($linha["nome_real"]) : "<span style='color: #ff6b6b; font-size: 0.9em;'>Ex-Usuário (ID: " . $linha['usuario_id'] . ")</span>";

                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($linha["id"]) . "</td>";
                                echo "<td>" . $nome_exibicao . "</td>";
                                echo "<td>" . htmlspecialchars($linha["ip_address"]) . "</td>";
                                echo "<td title='" . htmlspecialchars($linha["sql_command"]) . "'>" . substr(htmlspecialchars($linha["sql_command"]), 0, 50) . (strlen($linha["sql_command"]) > 50 ? '...' : '') . "</td>";
                                echo "<td>" . date("d/m/Y H:i", strtotime($linha["data_hora"])) . "</td>"; 
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:30px; color: #888;'>Nenhum registro encontrado.</td></tr>";
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
