<?php 
require __DIR__ . '/../controllers/validar_acesso.php'; 
require __DIR__ . '/../configs/conexao.php'; 
require __DIR__ . '/../components/modals/all_modals.php'; 

// Pegar filtros e paginação
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$filtro_marca = isset($_GET['filtro-marca']) ? trim($_GET['filtro-marca']) : '';
$filtro_setor = isset($_GET['filtro-setor']) ? trim($_GET['filtro-setor']) : '';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 10; // Mostrar 10 por página

// Buscar valores únicos para filtros rápidos (queries leves)
$marcas_lista = [];
$res_marcas = $conn->query("SELECT DISTINCT marca FROM maquinas WHERE marca IS NOT NULL AND marca != '' ORDER BY marca ASC");
if ($res_marcas) { while ($r = $res_marcas->fetch_assoc()) $marcas_lista[] = $r['marca']; }

$setores_lista = [];
$res_setores = $conn->query("SELECT DISTINCT setor FROM maquinas WHERE setor IS NOT NULL AND setor != '' ORDER BY setor ASC");
if ($res_setores) { while ($r = $res_setores->fetch_assoc()) $setores_lista[] = $r['setor']; }

// Construir cláusula WHERE
$where = "WHERE 1=1";
if ($busca_atual !== '') {
    $termo = $conn->real_escape_string($busca_atual);
    $where .= " AND (denominacao LIKE '%$termo%' OR numero_identificacao LIKE '%$termo%' OR modelo LIKE '%$termo%')";
}
if ($filtro_marca !== '') {
    $where .= " AND marca = '" . $conn->real_escape_string($filtro_marca) . "'";
}
if ($filtro_setor !== '') {
    $where .= " AND setor = '" . $conn->real_escape_string($filtro_setor) . "'";
}

// Contar total para paginação
$sql_count = "SELECT COUNT(*) as total FROM maquinas $where";
$res_count = $conn->query($sql_count);
$total_registros = $res_count ? $res_count->fetch_assoc()['total'] : 0;

$total_paginas = ceil($total_registros / $limite);
if ($total_paginas == 0) $total_paginas = 1;
if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;

$offset = ($pagina_atual - 1) * $limite;

// Buscar dados paginados
$sql = "SELECT id, denominacao, marca, modelo, numero_identificacao, ano_fabricacao, setor, criado_em
        FROM maquinas
        $where
        ORDER BY id DESC
        LIMIT $offset, $limite";
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
    <link rel="stylesheet" href="../../css/ticket.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../assets/icons/favicon.ico" type="image/x-icon">
    
    <style>
        /* Estilos específicos de Máquinas — apenas overrides da tabela e botões */
        .tabela-main td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid var(--corBordas);
            color: var(--corTxt3);
        }

        .tabela-main th {
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 15px;
        }

        .btnAcao {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            color: white;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
        }
        .btnAcao.editar { background: var(--editar); }
        .btnAcao.editar:hover { background: var(--editarHover); }
        .btnAcao.deletar { background: var(--corBase); }
        .btnAcao.deletar:hover { background: var(--corDestaque); }
        .btnAcao.ferramentas { background: var(--ferramentas); }
        .btnAcao.ferramentas:hover { background: var(--ferramentasHover); }
        .btnAcao.clipes { background: #64748b; }
        .btnAcao.clipes:hover { background: #475569; }
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
                        value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar denominação, NI ou modelo...">
                    <?php if ($busca_atual): ?>
                        <a href="?" class="page-clear-btn"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn-search">
                    <i class="bi bi-search"></i>
                </button>
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
            <button class="btn-page-action" onclick="showModal('adicaoMachine')">
                <i class="bi bi-gear"></i> Adicionar Máquinas
            </button>
        </div>

        <div class="tabela-bg2" id="tabe">
            <div class="tabela-titulo">
                <i class="bi bi-gear"></i>
                <h2>Máquinas</h2>
            </div>
            <div class="tabela-wrapper">
                <table class="tabela-main">
                    <thead>
                        <th>Denominação</th>
                        <th>Marca</th>
                        <th>N° Identificação</th>
                        <th>Ano</th>
                        <th>Setor</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </thead>
                    <tbody id="tabela-usuarios">
                        <?php
                        if ($resultado && $resultado->num_rows > 0) {
                            while ($linha = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                
                                // Mesclar denominação e modelo igual fizemos em preventiva.php
                                $denom = trim($linha["denominacao"]);
                                $modelo = trim($linha["modelo"]);
                                echo "<td><strong>" . htmlspecialchars($denom) . "</strong>";
                                if (!empty($modelo) && strcasecmp($modelo, $denom) !== 0) {
                                    echo "<br><small style='color: var(--corTxt2); opacity: 0.7;'>(" . htmlspecialchars($modelo) . ")</small>";
                                }
                                echo "</td>";

                                echo "<td>" . htmlspecialchars($linha["marca"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["numero_identificacao"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["ano_fabricacao"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["setor"]) . "</td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($linha["criado_em"])) . "</td>";
                        ?>
                                <?php
                                if ($permissao_usuario == "ADMIN") {
                                    echo "<td>
                                        <div style='display: flex; gap: 5px; justify-content: center;'>
                                        <button class='btnAcao editar' type='button' title='Editar Máquina' onclick=\"editarMaquina(" . $linha['id'] . ")\"><i class='bi bi-pencil-square'></i></button>
                                        <button class='btnAcao deletar' type='button' title='Excluir Máquina' onclick=\"showModal('dellMachine', " . $linha['id'] . ")\"><i class='bi bi-trash'></i></button>
                                        <button class='btnAcao ferramentas' type='button' title='Acessórios' onclick=\"showModal('modalAcessorios', " . $linha['id'] . ")\"><i class='bi bi-tools'></i></button>
                                        </div>
                                    </td>";
                                } else {
                                ?>
                                    <td>
                                        <div style='display: flex; gap: 5px; justify-content: center;'>
                                            <button class='btnAcao ferramentas' type='button' title='Acessórios'
                                                onclick="showModal('modalAcessorios', <?= $linha['id'] ?>)"><i class="bi bi-tools"></i></button>
                                            <button class='btnAcao clipes' type='button' title='Anexos' onclick="showModal('anexos', <?= $linha['id'] ?>)"><i
                                                    class="bi bi-paperclip"></i></button>
                                        </div>
                                    </td>
                                <?php } ?>
                        <?php
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align:center; padding: 20px;'>Nenhuma máquina encontrada para o termo pesquisado.</td></tr>";
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
                        'filtro-marca' => $filtro_marca,
                        'filtro-setor' => $filtro_setor,
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

    <script src="../../js/scripts.js?v=2" defer></script>
    <script src="../../js/processa_lotes_maquinas.js?v=2" defer></script>
</body>

</html>
