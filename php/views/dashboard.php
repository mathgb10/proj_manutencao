<?php require '../controllers/validar_acesso.php'; ?>
<!-- Validando se o cara está realmente logado -->
<!DOCTYPE html>
<html lang="pt-br" data-tema="claro">
<!-- NÃO TIRA O DATA-TEMA DE JEITO NENHUM -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SENAI MANUTENÇÃO</title>

    <!-- Estilização, BootstrapIcons e Favicon -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal_acesso.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">

</head>

<body>
    <?php
    if (isset($_GET['acesso']) && $_GET['acesso'] == 'negado') {
        require '../components/modal_acesso.php';
    }
    ?>

    <?php require '../components/nav.php'; ?>
    <!-- Colocando a Nav(SideBar) na página -->

    <section class="sec-main">

        <!-- Header -->
        <?php require '../components/header.php' ?>

        <!-- Cards -->
        <div class="card-box">
            <div class="card">
                <div class="card-header">
                    <p>Texto Um</p>
                    <i class="bi bi-info-square-fill"></i>
                </div>
                <div class="card-info">
                    <!-- Aqui entra o SELECT -->
                    <h1>1</h1>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <p>Texto Um</p>
                    <i class="bi bi-info-square-fill"></i>
                </div>
                <div class="card-info">
                    <!-- Aqui entra o SELECT -->
                    <h1>1</h1>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <p>Texto Um</p>
                    <i class="bi bi-info-square-fill"></i>
                </div>
                <div class="card-info">
                    <!-- Aqui entra o SELECT -->
                    <h1>1</h1>
                </div>
            </div>
        </div>

        <!-- Tabelas -->
        <div class="div-tabelas">
            <div class="tabela-bg">
                <table>
                    <thead>
                        <th>Coluna 1</th>
                        <th>Coluna 2</th>
                        <th>Coluna 3</th>
                        <th>Coluna 4</th>
                        <th>Coluna 5</th>
                        <th>Coluna 6</th>
                        <th>Coluna 7</th>
                        <th>Coluna 8</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8">REG</td>
                        </tr>
                        <tr>
                            <td colspan="8">REG</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="tabela-bg">
                <table>
                    <thead>
                        <th>Coluna 1</th>
                        <th>Coluna 2</th>
                        <th>Coluna 3</th>
                        <th>Coluna 4</th>
                        <th>Coluna 5</th>
                        <th>Coluna 6</th>
                        <th>Coluna 7</th>
                        <th>Coluna 8</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="8">REG</td>
                        </tr>
                        <tr>
                            <td colspan="8">REG</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>