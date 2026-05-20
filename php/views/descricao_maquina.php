<?php 
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php'; 

// --- Lógica de Paginação e Busca (Padrão Unificado) ---
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 8;

// 1. Contar total de registros com filtro (Usando conn_nr12)
$sql_count = "SELECT COUNT(*) as total FROM tipomaquina";
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn_nr12, $busca_atual);
    $sql_count .= " WHERE tipomaquina_nome LIKE '%$termo%' OR tipomaquina_arquivo LIKE '%$termo%'";
}
$res_count = $conn_nr12->query($sql_count);
$total_registros = $res_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);
if ($total_paginas == 0) $total_paginas = 1;
if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;

$offset = ($pagina_atual - 1) * $limite;

// 2. Buscar registros paginados
$sql = "SELECT * FROM tipomaquina";
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn_nr12, $busca_atual);
    $sql .= " WHERE tipomaquina_nome LIKE '%$termo%' OR tipomaquina_arquivo LIKE '%$termo%'";
}
$sql .= " ORDER BY tipomaquina_nome ASC LIMIT $limite OFFSET $offset";
$resultado = $conn_nr12->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descrição Máquinas - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../assets/icons/favicon.ico" type="image/x-icon">
    <script src="../../js/scripts.js" defer></script>

</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

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
            <button class="btn-page-action" onclick="showModal('adicaoTipoMaquina')">
                <i class="bi bi-plus-circle"></i> Nova Descrição
            </button>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo">
                <i class="bi bi-tags"></i>
                <h2>Descrição de Máquinas (NR12)</h2>
            </div>
            <div class="tabela-wrapper">
            <table class="tabela-main">
                <thead>
                    <th>Nome</th>
                    <th>Arquivo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </thead>
                <tbody id="tabela-descricao_maquinas">
                    <?php
                    if ($resultado && $resultado->num_rows > 0) {
                        while ($linha = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($linha["tipomaquina_nome"]) . "</td>";
                            echo "<td>" . htmlspecialchars($linha["tipomaquina_arquivo"]) . "</td>";

                            $status = strtolower($linha["tipomaquina_status"]);
                            $classe = ($status == 'ativo') ? 'status-ativo' : 'status-inativo';

                            echo "<td><span class='$classe'>" . htmlspecialchars($linha["tipomaquina_status"]) . "</span></td>";

                            echo "<td>
                                    <div style='display: flex; gap: 5px; justify-content: center;'>
                                        <button class='btnAcao editar' type='button' title='Editar' onclick=\"showModal('edicaoTipoMaquina', " . $linha['idtipomaquina'] . ")\"><i class='bi bi-pencil-square'></i></button>
                                        <button class='btnAcao deletar' type='button' title='Excluir' onclick=\"showModal('deletarTipoMaquina', " . $linha['idtipomaquina'] . ",'')\"><i class='bi bi-trash'></i></button>
                                        " . ($status == 'ativo' 
                                            ? "<button class='btnAcao deletar' title='Desativar' type='button' onclick=\"showModal('desativarTipMa', " . $linha['idtipomaquina'] . ",'')\"><i class='bi bi-x-lg'></i></button>"
                                            : "<button class='btnAcao confirmar' title='Ativar' type='button' onclick=\"showModal('ativarTipMa', " . $linha['idtipomaquina'] . ",'')\"><i class='bi bi-check-lg'></i></button>") . "
                                    </div>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding: 30px; color: #888;'>Nenhuma descrição encontrada.</td></tr>";
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

</body>

</html>
