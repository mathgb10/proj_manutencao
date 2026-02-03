<!DOCTYPE html>
<html lang="pt-br" data-tema="claro">
<!-- NÃO TIRA O DATA-TEMA DE JEITO NENHUM -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SENAI MANUTENÇÃO</title>

    <!-- Estilização, BootstrapIcons e Favicon -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/modal_acesso.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

</head>

<body class="body_login">

    <?php
    if (isset($_GET['acesso']) && $_GET['acesso'] == 'negado') {
        require 'php/components/modals/modal_acesso.php';
    }

    if (isset($_GET['erro'])) {
        if ($_GET['erro'] == 'email') {
            $erro_email_msg = "<div class='div-msg-erro'><p>E-mail não encontrado! Tente novamente</p></div>";
        } else {
            $erro_pass_msg = "<div class='div-msg-erro'><p>Senha incorreta! Tente novamente</p></div>";
        }
    }
    ?>

    <div class="login-bg">
        <div class="login-box">
            <form class="login-form" action="php/actions/auth_login.php" method="POST">
                <div class="div-img" id="">
                    <img src="assets/imgs/senailogo.png" alt="Logo Senai" id="senai-logo" style="width: 70%;">
                </div>
                <?php if (isset($erro_pass_msg)) echo $erro_pass_msg; ?>
                <?php if (isset($erro_email_msg)) echo $erro_email_msg; ?>
                <div class="div-inputs-chefe">
                    <div class="div-input">
                        <i class="bi bi-envelope-fill"></i>
                        <input type="email" id="email" name="email" placeholder="E-mail" class="input">
                        <button style="visibility: hidden;"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <div class="div-input">
                        <i class="bi bi-shield-fill"></i>
                        <input type="password" id="senha" name="senha" placeholder="*****" class="input">
                        <button type="button" onclick="showPass()" id="btnEye"><i class="bi bi-eye-fill"></i></button>
                    </div>

                    <div class=" div-btn">
                        <button type="submit" class="btn">Entrar <i class="bi bi-box-arrow-in-right"></i></button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <!-- Carregando Js na página -->
    <script src="js/scripts.js" defer>
    </script>
</body>

</html>