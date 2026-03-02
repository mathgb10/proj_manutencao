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

    <style>
        .quick-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            margin-left: 35px;
        }

        .btn-quick {
            background-color: var(--corBase);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-quick:hover {
            background-color: var(--corDestaque);
            transform: translateY(-2px);
        }

        .card {
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            /* Ensure it behaves like a link */
            color: inherit;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php require '../components/nav.php'; ?>
    <section class="sec-main">

        <!-- Header -->
            <?php require '../components/header.php'; ?>

        <div class="welcome-box">
            <h1>Bem-vindo ao Sistema de Manutenção</h1>
            <p>Este sistema foi desenvolvido para otimizar o controle de máquinas do SENAI. <br>Veja abaixo como começar
                a
                utilizar a plataforma:</p>
        </div>

        <div class="quick-actions">
            <?php if ($permissao_usuario == 'ADMIN') { ?>
                <a href="maquinas.php" class="btn-quick">
                    <i class="bi bi-plus-circle"></i> Nova Máquina
                </a>
                <a href="usuarios.php" class="btn-quick">
                    <i class="bi bi-people"></i> Gerenciar Usuários
                </a>
            <?php } else { ?>
                <a href="maquinas.php" class="btn-quick">
                    <i class="bi bi-search"></i> Buscar Máquinas
                </a>
            <?php } ?>
            <button class="btn-quick" onclick="showModal('modalFAQ')" style="border: none; cursor: pointer;">
                <i class="bi bi-question-circle"></i> Dúvidas Frequentes
            </button>
        </div>

        <div class="card-box">

            <a href="../views/maquinas.php" class="card maquinas-card">
                <div class="card-header">
                    <span>1. Cadastrar Máquinas</span>
                    <i class="bi bi-pc-display-horizontal" style="color: var(--corTxt3);"></i>
                </div>
                <div class="card-info-tutorial">
                    O primeiro passo é registrar o inventário. Acesse a aba <strong>Máquinas</strong> para cadastrar
                    novos equipamentos, informando modelo, setor e especificações técnicas para controle.
                </div>
            </a>

            <a href="#" class="card chamados-card" onclick="alert('Funcionalidade em desenvolvimento')">
                <div class="card-header">
                    <span>2. Abrir Chamados</span>
                    <i class="bi bi-ticket-perforated-fill" style="color: var(--corTxt3);"></i>
                </div>
                <div class="card-info-tutorial">
                    Identificou um defeito? Vá até a seção de <strong>Ordens de Serviço</strong>. Lá você pode abrir um
                    chamado detalhando o problema para que a equipe técnica seja notificada imediatamente.
                </div>
            </a>

            <div class="card status-card">
                <div class="card-header">
                    <span>3. Acompanhar Status</span>
                    <i class="bi bi-clipboard-data-fill" style="color: var(--corTxt3);"></i>
                </div>
                <div class="card-info-tutorial">
                    Acompanhe em tempo real a resolução. Os cards mudam de cor conforme o status (Pendente, Em
                    Manutenção, Concluído), garantindo transparência no processo de reparo.
                </div>
            </div>

        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>