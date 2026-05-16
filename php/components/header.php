<div class="div-header">
    <div class="div-img-header">
        <div class="avatar">
            <?php if (!empty($_SESSION['user_foto']) && file_exists("../../uploads/perfis/" . $_SESSION['user_foto'])): ?>
                <img src="../../uploads/perfis/<?= $_SESSION['user_foto'] ?>" alt="Foto de Perfil" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            <?php else: ?>
                <i class="bi bi-person"></i>
            <?php endif; ?>
        </div>
        <h4 style="color: var(--corTxt3)">Olá, <span
                style="color: var(--corDestaque);"><?php echo $nome_usuario ?></span></h4>
    </div>
    <div class="div-txt-header">
        <p>
            <i class="bi bi-calendar3"></i><?php echo date('d/m/Y') ?>
        </p>
    </div>
</div>