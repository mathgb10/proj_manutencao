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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">

</head>

<body>
    <?php require '../components/nav.php'; ?>
    <!-- Colocando a Nav(SideBar) na página -->

    <section class="sec-main">

        <!-- Header -->
        <?php require '../components/header.php' ?>


    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>