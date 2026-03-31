<?php
require_once __DIR__ . "/../configs/conexao.php";
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

                <a href="corretiva.php" style="display: none;"
                    class="<?php if ($atualmente_em == 'corretiva.php')
    echo 'ativo'; ?> links-sub">
                    <i class="bi bi-wrench"></i> Corretiva
                </a>

            </div>
        </div>

        <div class="menu-maquinas">

            <a href="javascript:void(0)" class="links maquinas-btn" id="btn-maquinas">
                <div>
                    <i class="bi bi-cpu"></i>
                    <span>Máquinas</span>
                </div>
                <i class="bi bi-caret-down-fill seta"></i>
            </a>

            <div class="submenu" id="submenu-maquinas">

                <a href="tipo_maquina.php"
                    class="<?php if ($atualmente_em == 'tipo_maquina.php')
    echo 'ativo'; ?> links-sub">
                    <i class="bi bi-tags-fill"></i> Tipo Máquinas
                </a>

                <a href="maquinas.php" class="<?php if ($atualmente_em == 'maquinas.php')
    echo 'ativo'; ?> links-sub">
                    <i class="bi bi-gear-fill"></i> Máquinas
                </a>

                <a href="motores.php" class="<?php if ($atualmente_em == 'motores.php')
    echo 'ativo'; ?> links-sub">
                    <i class="bi bi-lightning-fill"></i> Motores
                </a>

            </div>
        </div>

        <a href="gerencias_os.php" class="<?php if ($atualmente_em == 'gerencias_os.php')
    echo 'ativo'; ?> links">
            <i class="bi bi-clipboard-data-fill"></i> Gerenciar O.S
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

        <?php
} ?>

    </div>

    <div class="div-configs">
        <div>
            <button onclick="changeTheme()" id="tema"></button>

            <button id="notificacao" onclick="showModal('notificacao-modal')">
                <i class="bi bi-bell-fill"></i>
                <div class="div-noti" style="color: var(--corFundo2);">0</div>
            </button>
        </div>

        <button onclick="window.location.href='../actions/logout.php'" class="btn sair"
            onmouseover="changeSairBtn('open')" onmouseleave="changeSairBtn('closed')">
            <span>Sair</span> <i class="bi bi-door-closed-fill"></i>
        </button>
    </div>
</nav>

<style>
    .submenu {
        display: none;
        margin-left: 28px;
    }

    .submenu.aberto {
        display: block;
    }

    .links-sub {
        margin-top: 4px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        const dropdowns = [
            {
                btnId: "btn-manutencao",
                submenuId: "submenu-manutencao",
                paginas: ["preventiva.php", "corretiva.php"]
            },
            {
                btnId: "btn-maquinas",
                submenuId: "submenu-maquinas",
                paginas: ["tipo_maquinas.php", "maquinas.php", "motores.php"]
            }
        ];

        const paginaAtual = window.location.pathname;

        dropdowns.forEach(({ btnId, submenuId, paginas }) => {
            const btn = document.getElementById(btnId);
            const submenu = document.getElementById(submenuId);
            if (!btn || !submenu) return;

            const menu = btn.parentElement;

            function abrir() {
                submenu.classList.add("aberto");
                menu.classList.add("aberto");
                btn.classList.add("ativo");
            }

            function fechar() {
                submenu.classList.remove("aberto");
                menu.classList.remove("aberto");
                btn.classList.remove("ativo");
            }

            btn.addEventListener("click", (e) => {
                e.preventDefault();
                submenu.classList.contains("aberto") ? fechar() : abrir();
            });

            if (paginas.some(p => paginaAtual.includes(p))) {
                abrir();
            }
        });

    });
</script>
