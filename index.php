<!DOCTYPE html >
<html lang="pt-br" data-tema="claro">
<!-- NÃO TIRA O DATA-TEMA DE JEITO NENHUM -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SENAI MANUTENÇÃO</title>
    
    <!-- Estilização, BootstrapIcons e Favicon -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

</head>
<body>
    <div class="login-bg">
        <div class="login-box">
            <form class="login-form" action="php/actions/auth_login.php" method="POST">
                <div class="div-img">
                    <img src="assets/imgs/senailogo.png" alt="Logo Senai">
                </div>
                <div class="div-inputs-chefe">
                    <div class="div-input">
                        <i class="bi bi-envelope-fill"></i>
                        <input type="email" id="email" name="email" placeholder="E-mail" class="input">
                        <button style="visibility: hidden;" type="button" onclick="showPass()" id="btnEye"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <div class="div-input">
                        <i class="bi bi-shield-fill"></i>
                        <input type="password" id="senha" name="senha" placeholder="*****" class="input">
                        <button type="button" onclick="showPass()" id="btnEye"><i class="bi bi-eye-fill"></i></button>
                    </div>
                    <div class="div-btn">
                        <button type="submit" class="btn">Entrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Carregando Js na página -->
    <script src="js/scripts.js" defer></script>
</body>
</html>