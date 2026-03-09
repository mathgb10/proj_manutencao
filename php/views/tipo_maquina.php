<?php require '../controllers/validar_acesso.php'; ?>
<?php require '../configs/conexao.php'; ?>
<?php require '../components/modals/all_modals.php'; ?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipo Máquinas - SENAI MANUTENÇÃO</title>

    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
    <script src="../../js/scripts.js" defer></script>

</head>

<body>
    <?php require '../components/nav.php'; ?>

    <section class="sec-main">

        <?php require '../components/header.php'; ?>

        <div class="div-btns-pages">

            <form action="" method="GET" class="form-pesquisa">
                <div class="search-container">
                    <?php
                    $busca_atual = isset($_GET['search']) ? $_GET['search'] : '';
                    ?>
                    <div class="box-pesquisa">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" name="search" id="pesquisa" value="<?php echo htmlspecialchars($busca_atual); ?>"
                            placeholder="Pesquisar..." class="input-pesquisa">

                        <?php if ($busca_atual): ?>
                            <a href="<?php echo $_SERVER['PHP_SELF'] ?>" class="btn-clear-search"><i class="bi bi-x-lg"></i></a>
                        <?php endif; ?>
                    </div>
                    <!-- Hidden submit button to allow Enter to search -->
                    <button type="submit" style="display: none;"></button>
                </div>
            </form>

            <button class="btn" onclick="showModal('adicaoTipoMaquina')">Tipo Máquina <i
                    class="bi bi-plus-circle"></i></button>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo">
                <i class="bi bi-tags"></i>
                <h2>Tipo de Máquina (NR12)</h2>
            </div>
            <div class="tabela-wrapper">
            <table class="tabela-main">
                <thead>
                    <th>Nome</th>
                    <th>Arquivo</th>
                    <th>Status</th>
                    <th>Ações</th>
                </thead>
                <tbody id="tabela-tipo_maquinas">
                    <?php
                    if (!empty($busca_atual)) {
                        $termo_seguro = $conn_nr12->real_escape_string($busca_atual);

                        $sql = "SELECT * FROM tipomaquina WHERE 
                                tipomaquina_nome LIKE '%$termo_seguro%' OR 
                                tipomaquina_arquivo LIKE '%$termo_seguro%'";
                    } else {
                        $sql = "SELECT * FROM tipomaquina";
                    }

                    $resultado = $conn_nr12->query($sql);

                    if ($resultado && $resultado->num_rows > 0) {
                        while ($linha = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $linha["tipomaquina_nome"] . "</td>";
                            echo "<td>" . $linha["tipomaquina_arquivo"] . "</td>";

                            $status = strtolower($linha["tipomaquina_status"]);

                            if ($status == 'ativo') {
                                $classe = 'status-ativo';
                            } else {
                                $classe = 'status-inativo';
                            }

                            echo "<td><span class='$classe'>" . $linha["tipomaquina_status"] . "</span></td>";

                            echo "<td>
                                    <div style='display: flex; gap: 5px; justify-content: center;'>
                                        <button class='btnAcao editar' type='button' onclick=\"showModal('edicaoTipoMaquina', " . $linha['idtipomaquina'] . ")\"><i class='bi bi-pencil-square'></i></button>
                                        <button class='btnAcao deletar' type='button' onclick=\"showModal('deletarTipoMaquina', " . $linha['idtipomaquina'] . ",'')\"><i class='bi bi-trash'></i></button>
                                        <button class='btnAcao deletar' type='button' onclick=\"showModal('desativarTipMa', " . $linha['idtipomaquina'] . ",'')\"><i class='bi bi-x-lg'></i></button>
                                    </div>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding:15px;'>Nenhum tipo de máquina encontrado no NR12.</td></tr>";
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

</body>

</html>
