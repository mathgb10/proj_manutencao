<?php require '../controllers/validar_acesso.php'; ?>
<?php require '../components/modals/all_modals.php'; ?>
<?php require_once "../configs/conexao.php"; ?>
<!-- Validando se o cara está realmente logado -->
<!DOCTYPE html>
<html lang="pt-br" data-tema="">
<!-- NÃO TIRA O DATA-TEMA DE JEITO NENHUM -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SENAI MANUTENÇÃO</title>

    <!-- Estilização, BootstrapIcons e Favicon -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon">
</head>

<body>

    <?php require '../components/nav.php'; ?>
    <!-- Colocando a Nav(SideBar) na página -->

    <section class="sec-main" style="padding-bottom: 10%;">

        <!-- Header -->
            <?php require '../components/header.php'; ?>

        <!-- Cards -->
        <div class="card-box">
            <?php
            // Counts
            $count_maquinas = $conn->query("SELECT COUNT(*) as total FROM maquinas")->fetch_assoc()['total'];
            $count_usuarios = $conn->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];
            // Check if acessorios table exists before counting to avoid error if previous step failed
            $check_acessorios = $conn->query("SHOW TABLES LIKE 'acessorios'");
            if ($check_acessorios && $check_acessorios->num_rows > 0) {
                $count_acessorios = $conn->query("SELECT COUNT(*) as total FROM acessorios")->fetch_assoc()['total'];
            } else {
                $count_acessorios = 0;
            }
            ?>
            <div class="card card-green" style="height: 150px;">
                <div class="card-header">
                    <p>Máquinas</p>
                    <i class="bi bi-gear-wide-connected"></i>
                </div>
                <div class="card-info">
                    <h3><?= $count_maquinas ?></h3>
                </div>
            </div>
            <div class="card card-green" style="height: 150px;">
                <div class="card-header">
                    <p>Usuários</p>
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="card-info">
                    <h3><?= $count_usuarios ?></h3>
                </div>
            </div>
            <div class="card card-green" style="height: 150px;">
                <div class="card-header">
                    <p>Acessórios</p>
                    <i class="bi bi-tools"></i>
                </div>
                <div class="card-info">
                    <h3><?= $count_acessorios ?></h3>
                </div>
            </div>
        </div>

        <div class="div-dad">
            <h3><i class="bi bi-wrench-adjustable"></i> - Manutenção Corretivas Ativas</h3>

            <div class="div-dad-content">
                <div class="div-row">
                    <div class="deletar status">

                    </div>
                    <div class="div-items">
                        <div>
                            <p>Equipamento</p>
                        </div>
                        <div>
                            Fresa CNC - ROMI D 600
                        </div>
                    </div>
                    <div class="div-items">
                        <div>
                            <p>Problema</p>
                        </div>
                        <div>
                            Falha no Sensor de Eixo Z
                        </div>
                    </div>
                    <div class="div-items">
                        <div>
                            <p>Técnico</p>
                        </div>
                        <div>
                            Carlos Alberto
                        </div>
                    </div>
                    <div class="div-items">
                        <div>
                            <p>Parada Desde</p>
                        </div>
                        <div>
                            Hoje, 07:45
                        </div>
                    </div>

                </div>

                <div class="div-btn-adc">
                    <button class="btn" onclick="window.location.href='gerencias_os.php'">Acompanhar O.S<i class="bi bi-h-square-fill"></i></button>
                </div>
            </div>
        </div>

        <div class="div-dad">
            <h3><i class="bi bi-clock-fill"></i> - Preventivas Ativas</h3>

            <div class="div-dad-content">
                <div class="div-row">
                    <div class="confirmar status">

                    </div>
                    <div class="div-items">
                        <div>
                            <p>Equipamento</p>
                        </div>
                        <div>
                            Fresa CNC - ROMI D 600
                        </div>
                    </div>
                    <div class="div-items">
                        <div>
                            <p>NI / PATRIOTA</p>
                        </div>
                        <div>
                            Falha no Sensor de Eixo Z
                        </div>
                    </div>
                    <div class="div-items">
                        <div>
                            <p>Vencimento</p>
                        </div>
                        <div>
                            Carlos Alberto
                        </div>
                    </div>

                </div>

                <div class="div-btn-adc">
                    <button class="btn" onclick="window.location.href='preventiva.php'">Abrir Checklist<i class="bi bi-clipboard-check"></i></button>
                </div>
            </div>
        </div>

        <div class="div-dad">
            <h3><i class="bi bi-clock-fill"></i> - Preventivas Programadas</h3>

            <div class="div-dad-content">
                <div class="div-row">
                    <div class="confirmar status">

                    </div>
                    <div class="div-items">
                        <div>
                            <p>Equipamento</p>
                        </div>
                        <div>
                            Fresa CNC - ROMI D 600
                        </div>
                    </div>
                    <div class="div-items">
                        <div>
                            <p>NI / PATRIOTA</p>
                        </div>
                        <div>
                            Falha no Sensor de Eixo Z
                        </div>
                    </div>
                    <div class="div-items">
                        <div>
                            <p>Vencimento</p>
                        </div>
                        <div>
                            Carlos Alberto
                        </div>
                    </div>

                </div>

                <div class="div-btn-adc">
                    <button class="btn" onclick="window.location.href='preventiva.php'">Abrir Preventivas<i class="bi bi-clipboard-check-fill"></i></button>
                </div>
            </div>
        </div>

    </section>

    <script src="../../js/scripts.js" defer></script>
    <?php if (isset($_SESSION['user_senha_padrao']) && $_SESSION['user_senha_padrao'] == 1) { ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('changePassword');
                if (modal) {
                    modal.style.display = 'flex';
                }
            });
        </script>
    <?php }; ?>
</body>

</html>