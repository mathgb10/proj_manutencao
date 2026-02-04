<?php require '../controllers/validar_acesso.php'; ?>
<!DOCTYPE html>
<html lang="pt-br" data-tema="claro">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Tutorial SENAI</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">

    <style>
        /* --- CSS ESPECÍFICO PARA A TELA DE TUTORIAL --- */

        /* Centraliza tudo e garante que o fundo cubra a tela */
        .sec-main {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            height: 100vh;
            padding-top: 40px;
        }

        /* Texto de boas vindas */
        .welcome-box {
            text-align: center;
            width: 80%;
            margin-bottom: 40px;
            color: var(--corTxt3);
        }

        .welcome-box h1 {
            font-size: var(--text-4xl);
            margin-bottom: 10px;
            color: var(--corBase);
        }

        /* Ajustes no Container dos Cards para não bugar */
        .card-box {
            width: 90%;
            height: auto;
            /* Mudado de 20% para auto para caber o texto */
            min-height: 250px;
            display: flex;
            justify-content: space-between;
            /* Espalha os cards */
            gap: 20px;
            /* Garante espaço entre eles se a tela diminuir */
        }

        /* Adaptação do Card original para Texto Explicativo */
        .card {
            /* Forçamos uma altura fixa para ficarem alinhados */
            height: 280px;
            width: 32%;
            /* Divide em 3 colunas iguais com folga */
            padding: 20px;
        }

        /* Ícones grandes e bonitos */
        .card-header i {
            font-size: 2.5rem;
            color: var(--corBase);
        }

        .card-header span {
            font-weight: bold;
            font-size: var(--text-xl);
        }

        /* SOBRESCREVENDO A FONTE GIGANTE DO CSS ORIGINAL */
        /* O original tem 48px, aqui usamos 16px para ler o tutorial */
        .card-info-tutorial {
            color: var(--corTxt3);
            font-size: var(--text-base);
            line-height: 1.6;
            margin-top: 15px;
            text-align: justify;
        }
    </style>
</head>

<body>
    <?php require '../components/nav.php'; ?>
    <section class="sec-main">

        <?php require '../components/header.php' ?>

        <div class="welcome-box">
            <h1>Bem-vindo ao Sistema de Manutenção</h1>
            <p>Este sistema foi desenvolvido para otimizar o controle de máquinas do SENAI. <br>Veja abaixo como começar a utilizar a plataforma:</p>
        </div>

        <div class="card-box">

            <div class="card">
                <div class="card-header">
                    <span>1. Cadastrar Máquinas</span>
                    <i class="bi bi-pc-display-horizontal" style="color: var(--corTxt3);"></i>
                </div>
                <div class="card-info-tutorial">
                    O primeiro passo é registrar o inventário. Acesse a aba <strong>Máquinas</strong> para cadastrar novos equipamentos, informando modelo, setor e especificações técnicas para controle.
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span>2. Abrir Chamados</span>
                    <i class="bi bi-ticket-perforated-fill" style="color: var(--corTxt3);"></i>
                </div>
                <div class="card-info-tutorial">
                    Identificou um defeito? Vá até a seção de <strong>Ordens de Serviço</strong>. Lá você pode abrir um chamado detalhando o problema para que a equipe técnica seja notificada imediatamente.
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <span>3. Acompanhar Status</span>
                    <i class="bi bi-clipboard-data-fill" style="color: var(--corTxt3);"></i>
                </div>
                <div class="card-info-tutorial">
                    Acompanhe em tempo real a resolução. Os cards mudam de cor conforme o status (Pendente, Em Manutenção, Concluído), garantindo transparência no processo de reparo.
                </div>
            </div>

        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>