<?php require __DIR__ . '/../controllers/validar_acesso.php'; ?>
<?php require __DIR__ . '/../configs/conexao.php'; ?>
<?php require __DIR__ . '/../components/modals/all_modals.php'; ?>

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
    <link rel="stylesheet" href="../../js/scripts.js">
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <!-- Header -->
        <?php require __DIR__ . '/../components/header.php'; ?>

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
                    <div class="filtrar-status">
                        <label for="">Status:</label>
                        <select id="select-filtro-maquinas" name="filtro-status" onchange="filtrarMaquinas()">
                            <option value="todos">Todos</option>
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <!-- Hidden submit button to allow Enter to search -->
                    <button type="submit" style="display: none;"></button>
                </div>
            </form>
            <button class="btn" onclick="showModal('adicaoMachine')">
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
                        <th>Modelo</th>
                        <th>N° Identificação</th>
                        <th>N° Série</th>
                        <th>Ano</th>
                        <th>Setor</th>
                        <th>Criado em</th>
                        <th>Ações</th>
                    </thead>
                    <tbody id="tabela-usuarios">
                        <?php
                        // Carregamos todos os registros para permitir a pesquisa em tempo real (Live Search) via JS
                        $sql = "SELECT * FROM maquinas";
                        $resultado = $conn->query($sql);

                        if ($resultado && $resultado->num_rows > 0) {
                            while ($linha = $resultado->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($linha["denominacao"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["marca"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["modelo"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["numero_identificacao"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["numero_serie"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["ano_fabricacao"]) . "</td>";
                                echo "<td>" . htmlspecialchars($linha["setor"]) . "</td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($linha["criado_em"])) . "</td>";
                        ?>
                                <?php
                                if ($permissao_usuario == "ADMIN") {
                                    echo "<td>
                                        <div>
                                        <button class='btnAcao editar' type='button' title='Editar Máquina' onclick=\"editarMaquina(" . $linha['id'] . ")\"><i class='bi bi-pencil-square'></i></button>
                                        <button class='btnAcao deletar' type='button' title='Excluir Máquina' onclick=\"showModal('dellMachine', " . $linha['id'] . ")\"><i class='bi bi-trash'></i></button>
                                        <button class='btnAcao ferramentas' type='button' title='Acessórios' onclick=\"showModal('modalAcessorios', " . $linha['id'] . ")\"><i class='bi bi-tools'></i></button>
                                        </div>
                                    </td>";
                                } else {
                                ?>
                                    <td>
                                        <div>
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
                            echo "<tr><td colspan='9' style='text-align:center; padding: 20px;'>Nenhuma máquina encontrada para o termo pesquisado.</td></tr>";
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

    <script src="../../js/scripts.js?v=2" defer></script>
    <script src="../../js/processa_lotes_maquinas.js?v=2" defer></script>
</body>

</html>