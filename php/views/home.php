<?php require '../controllers/validate_access.php'; ?>
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
        <div class="div-header">
            <div class="div-img-header">
                <h2>Sistema de Manutenção</h2>
            </div>
            <div class="div-txt-header">
                <p>
                    <span id="msg_especial"></span>! <?php echo $nome_usuario; ?>
                    <br>
                    Espero que o tenha uma ótima experiencia em nosso sistema.
                </p>
                <div class="avatar"><?php echo substr($nome_usuario, 0, 2); ?></div>
            </div>
        </div>


    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>