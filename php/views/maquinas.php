<?php require '../controllers/validar_acesso.php'; ?>
<?php require '../configs/conexao.php'; ?>
<?php require '../components/modals/modais_add_usuario.php' ?>
<?php require '../components/modals/modal_edit_machine.php' ?>

<!-- Validando se o cara está realmente logado -->
<!DOCTYPE html>
<html lang="pt-br" data-tema="claro">
<!-- NÃO TIRA O DATA-TEMA DE JEITO NENHUM -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Máquinas - SENAI MANUTENÇÃO</title>

    <!-- Estilização, BootstrapIcons e Favicon -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal_acesso.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">
</head>

<body>
    <?php require '../components/nav.php'; ?>
    <!-- Colocando a Nav(SideBar) na página -->

    <section class="sec-main">

        <!-- Header -->
        <div class="div-header">
            <div class="div-img-header">
                <h2>Painel de Controle de Máquinas</h2>
            </div>
            <div class="div-txt-header">
                <p>
                    <span id="msg_especial"></span> <?php echo $nome_usuario; ?>
                    <br>
                    Esperamos que tenha uma ótima experiência em nosso sistema.
                </p>
                <div class="avatar"><i class="bi bi-person"></i></div>
            </div>
        </div>

        <!-- Btns -->
        <div class="div-btns-pages">
            <div>
                <label for="pesquisa"><i class="bi bi-search"></i></label>
                <input type="text" name="pesquisa" id="pesquisa">
            </div>
            <button class="btn" onclick="showModal('adicaoMaquia')">
                Adicionar Máquinas <i class="bi bi-gear"></i>
            </button>
        </div>

        <!-- Tabela -->
        <div class="tabela-bg2">
            <table class="tabela-main">
                <thead>
                    <th>Denominação</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Número de Identificação</th>
                    <th>Número de Série</th>
                    <th>Ano de Fabricação</th>
                    <th>Setor</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM maquinas ";
                    $resultado = $conn->query($sql);

                    if ($resultado && $resultado->num_rows > 0) {
                        while ($linha = $resultado->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $linha["denominacao"] . "</td>";
                            echo "<td>" . $linha["marca"] . "</td>";
                            echo "<td>" . $linha["modelo"] . "</td>";
                            echo "<td>" . $linha["numero_identificacao"] . "</td>";
                            echo "<td>" . $linha["numero_serie"] . "</td>";
                            echo "<td>" . $linha["ano_fabricacao"] . "</td>";
                            echo "<td>" . $linha["setor"] . "</td>";
                            echo "<td>" . $linha["criado_em"] . "</td>";
                            echo "<td>
                                        <button class='btnAcao editar' type='button' onclick='showModal('   '," . $linha['id'] . ")'><i class='bi bi-pencil-square'></i></button>
                                        <button class='btnAcao deletar' type='button' onclick='excluirMaquina(" . $linha['id'] . ")'><i class='bi bi-trash'></i></button>
                                        <button class='btnAcao confirmar' type='button' onclick='showModal('visualizar'," . $linha['id'] . ")'><i class='bi bi-eye'></i></button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan=10>Não existe Registro</td></tr>";
                    }

                    ?>
                </tbody>
            </table>
        </div>
        <div class="div-btns-change">
            <button type="button"><i class="bi bi-arrow-left"></i></button>
            <button type="button"><i class="bi bi-arrow-right"></i></button>
        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>