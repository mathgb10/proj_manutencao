<?php
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';
require __DIR__ . '/../components/modals/all_modals.php';

// --- Lógica de Paginação, Busca e Filtros ---
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$filtro_permissao = isset($_GET['filtro-permissao']) ? trim($_GET['filtro-permissao']) : '';
$pagina_atual = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($pagina_atual < 1)
    $pagina_atual = 1;
$limite = 8;

// Buscar valores únicos para filtro rápido
$permissoes_lista = [];
$r = $conn->query("SELECT DISTINCT permissao FROM usuarios WHERE permissao IS NOT NULL AND permissao != '' ORDER BY permissao ASC");
if ($r) { while ($row = $r->fetch_assoc()) $permissoes_lista[] = $row['permissao']; }

// 1. Construir WHERE
$where = "";
$conditions = [];
if (!empty($busca_atual)) {
    $termo = mysqli_real_escape_string($conn, $busca_atual);
    $conditions[] = "(nome LIKE '%$termo%' OR email LIKE '%$termo%' OR permissao LIKE '%$termo%')";
}
if ($filtro_permissao !== '') {
    $conditions[] = "permissao = '" . mysqli_real_escape_string($conn, $filtro_permissao) . "'";
}
if (!empty($conditions)) {
    $where = " WHERE " . implode(" AND ", $conditions);
}

// 2. Contar total de registros com filtro
$sql_count = "SELECT COUNT(*) as total FROM usuarios" . $where;
$res_count = $conn->query($sql_count);
$total_registros = $res_count->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $limite);
if ($total_paginas == 0)
    $total_paginas = 1;
if ($pagina_atual > $total_paginas)
    $pagina_atual = $total_paginas;

$offset = ($pagina_atual - 1) * $limite;

// 3. Buscar registros paginados
$sql = "SELECT * FROM usuarios" . $where . " ORDER BY nome ASC LIMIT $limite OFFSET $offset";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Usuários - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="../../css/global.css">
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
                        placeholder="Pesquisar nome ou e-mail...">
                    <?php if ($busca_atual): ?>
                        <a href="?" class="page-clear-btn"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>
                <?php if (!empty($permissoes_lista)): ?>
                <div class="page-filter-box">
                    <label>Permissão:</label>
                    <select name="filtro-permissao" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        <?php foreach ($permissoes_lista as $p): ?>
                            <option value="<?= htmlspecialchars($p) ?>" <?= $filtro_permissao === $p ? 'selected' : '' ?>><?= htmlspecialchars($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
            </form>
            <button class="btn-page-action" onclick="showModal('adicaoUser')">
                <i class="bi bi-person-add"></i> Adicionar Usuário
            </button>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo">
                <i class="bi bi-people"></i>
                <h2>Usuários</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main">
                    <thead>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Senha</th>
                        <th>Permissão</th>
                        <th>Ações</th>
                    <tbody id="tabela-usuarios">
                        <?php
                        if ($resultado && $resultado->num_rows > 0) {
                            while ($linha = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($linha["nome"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["email"]) . "</td>";
                                echo "<td>******</td>"; // Senha oculta
                                echo "<td> <div class='div-permissao'>" . htmlspecialchars($linha["permissao"]) . "</div></td>";

                                // Botões de Ação
                                echo "<td>
                                        <div style='display: flex; gap: 5px; justify-content: center;'>
                                            <button class='btnAcao editar' type='button' title='Editar Usuário' onclick=\"showModal('edicaoUser', " . $linha['id'] . ")\"><i class='bi bi-pencil-square'></i></button>
                                            <button class='btnAcao deletar' type='button' style='background-color: #ffc107; color: #000;' title='Resetar Senha' onclick=\"showModal('resetPass', " . $linha['id'] . ")\"><i class='bi bi-key-fill'></i></button>
                                            <button class='btnAcao deletar' type='button' title='Excluir Usuário' onclick=\"showModal('dell', " . $linha['id'] . ",'usuario')\"><i class='bi bi-trash'></i></button>
                                        </div>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #888;'>Nenhum usuário encontrado para esta pesquisa.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação Unificada -->
            <div class="page-pagination">
                <?php
                    $pag_params = http_build_query(array_filter([
                        'search' => $busca_atual,
                        'filtro-permissao' => $filtro_permissao,
                    ], fn($v) => $v !== ''));
                ?>
                <?php if ($pagina_atual > 1): ?>
                    <a href="?<?= $pag_params ?>&page=<?= $pagina_atual - 1 ?>" class="pag-btn"><i class="bi bi-chevron-left"></i> Anterior</a>
                <?php else: ?>
                    <span class="pag-btn disabled"><i class="bi bi-chevron-left"></i> Anterior</span>
                <?php endif; ?>

                <span class="pag-current">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <a href="?<?= $pag_params ?>&page=<?= $pagina_atual + 1 ?>" class="pag-btn">Próxima <i class="bi bi-chevron-right"></i></a>
                <?php else: ?>
                    <span class="pag-btn disabled">Próxima <i class="bi bi-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <script>
        // Tratamento de mensagens de sucesso/erro
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('msg')) {
                const msg = urlParams.get('msg');
                if (msg === 'senha_alterada') {
                    alert('✔ Senha do usuário alterada com sucesso!');
                } else if (msg === 'senha_resetada') {
                    alert('Senha alterada para senaisp por padrão.');
                }
                // Remove o parâmetro da URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            if (urlParams.has('erro')) {
                const erro = urlParams.get('erro');
                if (erro === 'senhas_nao_coincidem') {
                    alert('✗ As senhas informadas não coincidem!');
                } else if (erro === 'sem_permissao') {
                    alert('✗ Você não tem permissão para realizar esta ação!');
                } else if (erro === 'erro_banco') {
                    alert('✗ Erro ao processar a requisição no banco de dados!');
                }
                // Remove o parâmetro da URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>