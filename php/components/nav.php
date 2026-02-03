<?php
require_once "../configs/conexao.php";
$atualmente_em = basename($_SERVER['PHP_SELF']);
$permissao_usuario = $_SESSION['user_permissao'];
?>


<nav class="sidebar">
    <div class="botao-fechar">
        <button id="fechar-nav"><i class="bi bi-arrow-left-circle-fill"></i></button>
    </div>
    <div class="div-img">
        <img src="../../assets/imgs/senailogo.png" alt="Logo Senai" id="senai-logo2">
    </div>
    <div class="div-links">
        <a href="home.php" class="<?php if ($atualmente_em == 'home.php') echo "ativo" ?> links">Home <i class="bi bi-house-door-fill"></i></a>
        <a href="dashboard.php" class="<?php if ($atualmente_em == 'dashboard.php') echo "ativo" ?> links">Dashboard <i class="bi bi-speedometer2"></i></a>
        <a href="#" class="<?php if ($atualmente_em == 'um.php') echo "ativo" ?> links">?</a>
        <a href="#" class="<?php if ($atualmente_em == 'dois.php') echo "ativo" ?> links">?</a>
        <a href="maquinas.php" class="<?php if ($atualmente_em == 'maquinas.php') echo "ativo" ?> links">Painel de Máquinas<i class="bi bi-gear"></i></a>
        <?php
        if ($permissao_usuario == "ADMIN") {
        ?>
            <a href="../views/usuarios.php" class="<?php if ($atualmente_em == 'usuarios.php') echo "ativo" ?> links">Painel de Usuários<i class="bi bi-file-earmark-person-fill"></i></a>
        <?php
        }
        ?>
    </div>

    <div class="div-configs">
        <div>
            <button onclick="changeTheme()" id="tema"></button>
            <button id="notificacao">
                <i class="bi bi-bell-fill"></i>
                <div class="div-noti">0</div>
            </button>

        </div>
        <button onclick="window.location.href='../actions/logout.php'" class="btn sair" onmouseover="changeSairBtn('open')" onmouseleave="changeSairBtn('closed')">Sair <i class="bi bi-door-closed-fill"></i></button>
    </div>
</nav>

<div class="modal-fundo" style="display: none;">
    <div class="modal-notificacao">
        <div class="modal-header">
            <h3>Notificações</h3>
            <button id="fechar-modal" onclick="closeModal('notificao')"><i class='bi bi-x-circle-fill'></i></button>
        </div>
        <div class="modal-notificao-corpo">

        </div>
    </div>
</div>