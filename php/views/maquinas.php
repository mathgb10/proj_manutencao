<?php 
require __DIR__ . '/../controllers/validar_acesso.php'; 
require __DIR__ . '/../configs/conexao.php'; 
require __DIR__ . '/../components/modals/all_modals.php'; 

// Pegar filtros e paginação
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
$limite = 10; // Mostrar 10 por página

// Construir cláusula WHERE
$where = "WHERE 1=1";
if ($busca_atual !== '') {
    $termo = $conn->real_escape_string($busca_atual);
    $where .= " AND (denominacao LIKE '%$termo%' OR numero_identificacao LIKE '%$termo%' OR modelo LIKE '%$termo%')";
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
$sql = "SELECT * FROM maquinas $where ORDER BY id DESC LIMIT $offset, $limite";
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
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
    
    <style>
        /* Estilos da Pesquisa (Padrão Moderno) */
        .preventiva-search-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 20px;
        }

        .preventiva-form {
            flex: 1;
            max-width: 600px;
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

        .btn-novo-registro {
            height: 48px;
            display: inline-flex;
            align-items: center;
            background: #ff2d35;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0 25px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            gap: 10px;
            transition: 0.3s;
        }
        .btn-novo-registro:hover {
            background: #e01b22;
        }

        /* Estilos da Tabela Main */
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
        .btnAcao.editar { background: #3b82f6; }
        .btnAcao.editar:hover { background: #2563eb; }
        .btnAcao.deletar { background: #ef4444; }
        .btnAcao.deletar:hover { background: #dc2626; }
        .btnAcao.ferramentas { background: #8b5cf6; }
        .btnAcao.ferramentas:hover { background: #7c3aed; }
        .btnAcao.clipes { background: #64748b; }
        .btnAcao.clipes:hover { background: #475569; }

        /* Paginação Moderna */
        .preventiva-paginacao {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-top: 30px;
            padding: 10px 0;
        }

        .btn-ant-novo, .btn-prox-novo {
            color: #777;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }
        
        .btn-ant-novo:not(.disabled):hover, .btn-prox-novo:not(.disabled):hover {
            color: var(--status-danger, #ff2d35);
        }
        
        .btn-ant-novo.disabled, .btn-prox-novo.disabled {
            color: #444;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .pagina-atual-texto {
            background: #1e3a8a;
            color: #60a5fa;
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
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

        <!-- Botões e Pesquisa -->
        <div class="preventiva-search-header">
            <form action="" method="GET" class="preventiva-form">
                <div class="preventiva-search-box">
                    <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" id="pesquisa"
                        value="<?php echo htmlspecialchars($busca_atual); ?>" placeholder="Pesquisar denominação, NI ou modelo..."
                        class="preventiva-input">
                    <?php if ($busca_atual): ?>
                        <a href="?" class="btn-clear-search"><i class="bi bi-x-lg"></i></a>
                    <?php endif; ?>
                </div>
            </form>
            <button class="btn-novo-registro" onclick="showModal('adicaoMachine')">
                Adicionar Máquinas <i class="bi bi-gear"></i>
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
                        <th>N° Série</th>
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
                                echo "<td>" . htmlspecialchars($linha["numero_serie"]) . "</td>";
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
                            echo "<tr><td colspan='8' style='text-align:center; padding: 20px;'>Nenhuma máquina encontrada para o termo pesquisado.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginação Server-Side -->
            <div class="preventiva-paginacao">
                <?php if ($pagina_atual > 1): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&page=<?php echo $pagina_atual - 1; ?>" class="btn-ant-novo"><i class="bi bi-chevron-left"></i> Anterior</a>
                <?php else: ?>
                    <span class="btn-ant-novo disabled"><i class="bi bi-chevron-left"></i> Anterior</span>
                <?php endif; ?>

                <span class="pagina-atual-texto">Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></span>

                <?php if ($pagina_atual < $total_paginas): ?>
                    <a href="?search=<?php echo urlencode($busca_atual); ?>&page=<?php echo $pagina_atual + 1; ?>" class="btn-prox-novo">Próxima <i class="bi bi-chevron-right"></i></a>
                <?php else: ?>
                    <span class="btn-prox-novo disabled">Próxima <i class="bi bi-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        </div>

    </section>

    <script src="../../js/scripts.js?v=2" defer></script>
    <script src="../../js/processa_lotes_maquinas.js?v=2" defer></script>
</body>

</html>