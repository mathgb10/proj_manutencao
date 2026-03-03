<?php
require_once "../configs/conexao.php";
$atualmente_em = basename($_SERVER['PHP_SELF']);
$permissao_usuario = $_SESSION['user_permissao'];
?>

<nav class="sidebar">
    <div class="botao-fechar">
        <button id="fechar-nav">
            <i class="bi bi-arrow-left-circle-fill"></i>
        </button>
    </div>

    <div class="div-img">
        <img src="../../assets/imgs/senailogo2.png" alt="Logo Senai" id="senai-logo2">
    </div>

    <div class="div-links">

        <a href="home.php" class="<?php if ($atualmente_em == 'home.php')
            echo 'ativo'; ?> links">
            <i class="bi bi-house-door-fill"></i> Home
        </a>

        <a href="dashboard.php" class="<?php if ($atualmente_em == 'dashboard.php')
            echo 'ativo'; ?> links">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="menu-manutencao">

            <a href="javascript:void(0)" class="links manutencao-btn" id="btn-manutencao">
                <div>
                    <i class="bi bi-tools"></i>
                    <span>Manutenção</span>
                </div>
                <i class="bi bi-caret-down-fill seta"></i>
            </a>

            <div class="submenu" id="submenu-manutencao">

                <a href="preventiva.php"
                    class="<?php if ($atualmente_em == 'preventiva.php')
                        echo 'ativo'; ?> links-sub">
                    <i class="bi bi-clock-fill"></i> Preventiva
                </a>

                <a href="corretiva.php" class="<?php if ($atualmente_em == 'corretiva.php')
                    echo 'ativo'; ?> links-sub">
                    <i class="bi bi-wrench"></i> Corretiva
                </a>

            </div>
        </div>

        <a href="maquinas.php" class="<?php if ($atualmente_em == 'maquinas.php')
            echo 'ativo'; ?> links">
            <i class="bi bi-gear"></i> Painel de Máquinas
        </a>

        <?php if ($permissao_usuario == "ADMIN") { ?>

            <a href="../views/usuarios.php" class="<?php if ($atualmente_em == 'usuarios.php')
                echo 'ativo'; ?> links">
                <i class="bi bi-file-earmark-person-fill"></i> Painel de Usuários
            </a>

            <a href="log.php" class="<?php if ($atualmente_em == 'log.php')
                echo 'ativo'; ?> links">
                <i class="bi bi-person-vcard"></i> Painel de Logs
            </a>

            <a href="gerencias_os.php" class="<?php if ($atualmente_em == 'gerencias_os.php')
                echo 'ativo'; ?> links">
                <i class="bi bi-person-vcard"></i> Gerenciar O.S
            </a>

        <?php } ?>

    </div>

    <div class="div-configs">
        <div>
            <button onclick="changeTheme()" id="tema"></button>

            <button id="notificacao" onclick="showModal('notificacao-modal')">
                <i class="bi bi-bell-fill"></i>
                <div class="div-noti">0</div>
            </button>
        </div>

        <button onclick="window.location.href='../actions/logout.php'" class="btn sair"
            onmouseover="changeSairBtn('open')" onmouseleave="changeSairBtn('closed')">
            Sair <i class="bi bi-door-closed-fill"></i>
        </button>
    </div>
</nav>



<script>
    document.addEventListener("DOMContentLoaded", () => {

        const btnManutencao = document.getElementById("btn-manutencao");
        const submenu = document.getElementById("submenu-manutencao");
        const menuManutencao = btnManutencao.parentElement;

        function abrirMenu() {
            submenu.classList.add("aberto");
            menuManutencao.classList.add("aberto");
            btnManutencao.classList.add("ativo");
        }

        function fecharMenu() {
            submenu.classList.remove("aberto");
            menuManutencao.classList.remove("aberto");
            btnManutencao.classList.remove("ativo");
        }

        btnManutencao.addEventListener("click", (e) => {
            e.preventDefault();
            submenu.classList.contains("aberto") ? fecharMenu() : abrirMenu();
        });

        const paginaAtual = window.location.pathname;
        if (paginaAtual.includes("preventiva.php") || paginaAtual.includes("corretiva.php")) {
            abrirMenu();
        }

    });
</script>