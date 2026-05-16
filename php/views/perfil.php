<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}
require_once __DIR__ . "/../configs/conexao.php"; 

$id_usuario = $_SESSION['user_id'];
$nome_usuario = $_SESSION['user_nome'];
$permissao_usuario = $_SESSION['user_permissao'];
$foto_perfil = $_SESSION['user_foto'] ?? '';

// Buscar dados extras
$sql = "SELECT email FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$user_data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br" data-tema="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Manutenção</title>

    <link rel="stylesheet" href="../../css/global.css">
    <link rel="stylesheet" href="../../css/nav.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/modal.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" href="../../favicon.ico" type="image/x-icon">

    <style>
        .avatar-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }

        .avatar-profile {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: var(--corFundo2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: var(--corDestaque);
            border: 4px solid var(--corDestaque);
            overflow: hidden;
            object-fit: cover;
        }

        .upload-btn {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--corDestaque);
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: 2px solid white;
            transition: 0.3s;
        }

        .upload-btn:hover {
            scale: 1.1;
            background: var(--corBase);
        }

        #foto-input {
            display: none;
        }

        .profile-info {
            width: 100%;
            max-width: 600px;
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/../components/nav.php'; ?>

    <section class="sec-main dontmove">
        <?php require __DIR__ . '/../components/header.php'; ?>

        <div class="modal-box profile-info" style="margin-top: 50px;">
            <div class="modal-header">
                <h3>Meu Perfil</h3>
            </div>

            <form action="../actions/upload_foto.php" method="POST" enctype="multipart/form-data" class="modal-form">
                
                <div class="avatar-container">
                    <div class="avatar-profile">
                        <?php if (!empty($foto_perfil) && file_exists("../../uploads/perfis/" . $foto_perfil)): ?>
                            <img src="../../uploads/perfis/<?= $foto_perfil ?>" alt="Foto" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <?= substr($nome_usuario, 0, 2); ?>
                        <?php endif; ?>
                    </div>
                    <label for="foto-input" class="upload-btn">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                    <input type="file" name="foto" id="foto-input" onchange="this.form.submit()">
                </div>

                <div class="modal-row">
                    <div class="modal-input">
                        <label>Nome:</label>
                        <div class="input-wrapper">
                            <input type="text" disabled value="<?= $nome_usuario ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-row">
                    <div class="modal-input">
                        <label>E-mail:</label>
                        <div class="input-wrapper">
                            <input type="text" disabled value="<?= $user_data['email'] ?? 'N/A' ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-row">
                    <div class="modal-input">
                        <label>Cargo / Permissão:</label>
                        <div class="input-wrapper">
                            <input type="text" disabled value="<?= $permissao_usuario ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="justify-content: center; gap: 20px;">
                    <button type="button" class="btn-confirmar" onclick="window.location.href='home.php'" style="background: var(--corEscura);">
                        Voltar
                    </button>
                    <button type="button" class="btn-confirmar deletar" onclick="window.location.href='../actions/logout.php'">
                        Sair <i class="bi bi-door-closed-fill"></i>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <script src="../../js/scripts.js" defer></script>
</body>

</html>
