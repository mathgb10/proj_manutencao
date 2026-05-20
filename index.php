<!DOCTYPE html>
<html lang="pt-br" data-tema="">
<!-- NÃO TIRA O DATA-TEMA DE JEITO NENHUM -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SENAI MANUTENÇÃO</title>
    
    <!-- Estilização, BootstrapIcons e Favicon -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/modal.css">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">


    <!-- Biblioteca do QRCODE -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <!-- Biblioteca das Particulas -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
</head>

<body class="body_login">

    <?php
    if (isset($_GET['erro'])) {
        if ($_GET['erro'] == 'email') {
            $erro = "<div class='div-msg-erro'><p>E-mail não encontrado! Tente novamente</p></div>";
        } else if (isset($_GET['erro']) == 'pass'){
            $erro = "<div class='div-msg-erro'><p>Senha incorreta! Tente novamente</p></div>";
        } 
    }
    ?>

    <div class="login-bg">
        <div id="particles-js"></div>
        <div class="login-box">
            <form class="login-form" action="php/actions/auth_login.php" method="POST">
                <div class="div-img">
                    <img src="assets/imgs/senailogo.png" alt="Logo Senai" id="senai-logo" style="width: 70%;">
                </div>
                <?php if (isset($erro))
                    echo $erro; ?>
                <div class="div-inputs-chefe">
                    <div class="div-input">
                        <i class="bi bi-envelope-fill"></i>
                        <input type="email" id="email" name="email" placeholder="E-mail" class="input">
                        <button type="button" style="visibility: hidden;" id="btnScan1" class="btnEsp"><i class="bi bi-qr-code-scan"></i></button>
                    </div>
                    <div class="div-input">
                        <i class="bi bi-shield-fill"></i>
                        <input type="password" id="senha" name="senha" placeholder="*****" class="input">
                        <button type="button" onclick="showPass()" id="btnEyeLogin" class="btnEsp"><i class="bi bi-eye-fill"></i></button>
                    </div>

                    <div class="div-btn">
                        <button type="submit" class="btn">Entrar <i class="bi bi-box-arrow-in-right"></i></button>
                    </div>
                </div>

            </form>
        </div>
    </div>


    <!-- Carregando Js na página -->
    <script>
        particlesJS("particles-js", {
            particles: {
                number: {
                    value: 80,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: "#ff2b2b"
                },
                shape: {
                    type: "circle"
                },
                opacity: {
                    value: 0.5
                },
                size: {
                    value: 3,
                    random: true
                },
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#ff2b2b",
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2.5,
                    direction: "none",
                    out_mode: "out"
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: {
                        enable: true,
                        mode: "grab"
                    },
                    onclick: {
                        enable: true,
                        mode: "push"
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 140,
                        line_linked: {
                            opacity: 1
                        }
                    },
                    push: {
                        particles_nb: 4
                    }
                }
            },
            retina_detect: true
        });
    </script>
    <script src="js/scripts.js" defer></script>

</body>

</html>