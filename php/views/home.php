<?php require '../controllers/validar_acesso.php'; ?>
<?php require '../components/modals/all_modals.php'; ?>
<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Início - Tutorial SENAI</title>

    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../assets/icons/favicon.ico" type="image/x-icon">


</head>

<body>
    <?php require '../components/nav.php'; ?>
    <section class="sec-main">

        <?php require '../components/header.php'; ?>

        <div class="doc-container">

            <div class="doc-hero">
                <div class="hero-content">
                    <h1>Bem-vindo ao Sistema de Manutenção</h1>
                    <p>Este sistema foi desenvolvido para otimizar o controle de máquinas do SENAI.
                        Veja abaixo como começar a utilizar a plataforma.</p>
                </div>
            </div>

            <div class="doc-grid">

                <a href="../views/maquinas.php" class="doc-card" style="text-decoration: none;">
                    <div class="card-icon-box">
                        <i class="bi bi-pc-display-horizontal"></i>
                    </div>
                    <h2>1. Cadastrar Máquinas</h2>
                    <p>
                        O primeiro passo é registrar o inventário. Acesse a aba <strong>Máquinas</strong> para cadastrar
                        novos equipamentos, informando modelo, setor e especificações técnicas para controle.
                    </p>
                </a>

                <a href="#" class="doc-card" style="text-decoration: none;" onclick="alert('Funcionalidade em desenvolvimento')">
                    <div class="card-icon-box">
                        <i class="bi bi-ticket-perforated-fill"></i>
                    </div>
                    <h2>2. Abrir Chamados</h2>
                    <p>
                        Identificou um defeito? Vá até a seção de <strong>Ordens de Serviço</strong>. Lá você pode abrir um
                        chamado detalhando o problema para que a equipe técnica seja notificada imediatamente.
                    </p>
                </a>

                <a href="dashboard.php" class="doc-card" style="text-decoration: none;">
                    <div class="card-icon-box">
                        <i class="bi bi-clipboard-data-fill"></i>
                    </div>
                    <h2>3. Acompanhar Status</h2>
                    <p>
                        Acompanhe em tempo real a resolução. Os cards mudam de cor conforme o status (Pendente, Em
                        Manutenção, Concluído), garantindo transparência no processo de reparo.
                    </p>
                </a>

            </div>

        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>