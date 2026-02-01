<?php
$atualmente_em = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar">
    <div class="div-img">
        <img src="../../assets/imgs/senailogo.png" alt="Logo Senai">
    </div>
    <div class="div-links">
        <a href="home.php" class="<?php if ($atualmente_em == 'home.php') echo "ativo" ?> links">Home <i class="bi bi-house-door-fill"></i></a>
        <a href="dashboard.php" class="<?php if ($atualmente_em == 'dashboard.php') echo "ativo" ?> links">Dashboard <i class="bi bi-speedometer2"></i></a>
        <a href="#" class="<?php if ($atualmente_em == 'um.php') echo "ativo" ?> links">?</a>
        <a href="#" class="<?php if ($atualmente_em == 'dois.php') echo "ativo" ?> links">?</a>
        <a href="#" class="<?php if ($atualmente_em == 'tres.php') echo "ativo" ?> links">?</a>
        <?php
        if ($permissao_usuario == "ADMIN") {
        ?>
            <a href="../views/usuarios.php" class="<?php if ($atualmente_em == 'usuarios.php') echo "ativo" ?> links">Usuários</a>
        <?php
        }
        ?>
    </div>
    <div class="div-configs">
        <div>
            <button onclick="changeTheme()" id="tema"></button>
            <button id="notificacao"><i class="bi bi-bell-fill"></i><div class="div-noti">0</div></button>
        </div>
        <button onclick="window.location.href='../actions/logout.php'" class="btn sair" onmouseover="changeSairBtn('open')" onmouseleave="changeSairBtn('closed')">Sair <i class="bi bi-door-closed-fill"></i></button>
    </div>
</nav>