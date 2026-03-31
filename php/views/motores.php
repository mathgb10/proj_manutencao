<?php require __DIR__ . '/../controllers/validar_acesso.php'; ?>
<?php require __DIR__ . '/../configs/conexao.php'; ?>
<?php require __DIR__ . '/../components/modals/all_modals.php'; ?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motores - SENAI MANUTENÇÃO</title>

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
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main">

        <?php require __DIR__ . '/../components/header.php'; ?>
        
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

            <button class="btn" onclick="showModal('adicaoMotor')">Adicionar Motores <i
                    class="bi bi-plus-circle"></i></button>
        </div>

        <div class="tabela-bg2">
            <div class="tabela-titulo">
                <i class="bi bi-lightning"></i>
                <h2>Motores (NR12)</h2>
            </div>
            <div class="tabela-wrapper">
            <table class="tabela-main">
                <thead>
                    <th>Fabricante</th>
                    <th>Modelo</th>
                    <th>Potência</th>
                    <th>Tensão</th>
                    <th>Corrente</th>
                    <th>Status</th>
                    <th>Ações</th>
                </thead>
                <tbody id="tabela-motores">
                    <?php
                    if (!empty($busca_atual)) {
                        $termo_seguro = $conn_nr12->real_escape_string($busca_atual);

                        $sql = "SELECT * FROM motor WHERE
                            motor_fabricante LIKE '%$termo_seguro%' OR
                            motor_modelo LIKE '%$termo_seguro%' OR
                            motor_potencia LIKE '%$termo_seguro%' OR
                            `motor_tensão` LIKE '%$termo_seguro%' OR
                            motor_corrente LIKE '%$termo_seguro%'";
                    } else {
                        $sql = "SELECT * FROM motor";
                    }


                    $resultado = $conn_nr12->query($sql);

                    if ($resultado && $resultado->num_rows > 0) {
                        while ($linha = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $linha["motor_fabricante"] . "</td>";
                            echo "<td>" . $linha["motor_modelo"] . "</td>";
                            echo "<td>" . $linha["motor_potencia"] . "</td>";
                            echo "<td>" . $linha["motor_tensão"] . "</td>";
                            echo "<td>" . $linha["motor_corrente"] . "</td>";

                            $status = strtolower($linha["motor_status"]);

                            if ($status == 'ativo') {
                                $classe = 'status-ativo';
                            } else {
                                $classe = 'status-inativo';
                            }

                            echo "<td><span class='$classe'>" . $linha["motor_status"] . "</span></td>";

                            echo "<td>
                                    <div style='display: flex; gap: 5px; justify-content: center;'>
                                        <button class='btnAcao editar' type='button' onclick=\"showModal('editarMotor', " . $linha['idmotor'] . ")\"><i class='bi bi-pencil-square'></i></button>
                                        <button class='btnAcao deletar' type='button' onclick=\"showModal('deletarMotor', " . $linha['idmotor'] . ",'')\"><i class='bi bi-trash'></i></button>
                                        " . ($status == 'ativo' 
                                            ? "<button class='btnAcao deletar' title='Desativar' type='button' onclick=\"showModal('desativarMotor', " . $linha['idmotor'] . ",'')\"><i class='bi bi-x-lg'></i></button>"
                                            : "<button class='btnAcao confirmar' title='Ativar' type='button' onclick=\"showModal('ativarMotor', " . $linha['idmotor'] . ",'')\"><i class='bi bi-check-lg'></i></button>") . "
                                    </div>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' style='text-align:center; padding:15px;'>Nenhum motor encontrado no NR12.</td></tr>";
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

