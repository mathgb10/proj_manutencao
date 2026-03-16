<?php
require "../../configs/conexao.php";
require "../../controllers/validar_acesso.php";

// Verifica se é administrador
if ($_SESSION['user_permissao'] !== 'ADMIN') {
    header("Location: ../../views/usuarios.php?erro=sem_permissao");
    exit;
}

if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];
    $senha_padrao_texto = 'senaisp';
    $senha_cript = password_hash($senha_padrao_texto, PASSWORD_DEFAULT);
    $senha_padrao_flag = 1;

    $update = "UPDATE usuarios SET senha = ?, senha_padrao = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update);
    $stmt->bind_param("sii", $senha_cript, $senha_padrao_flag, $id_usuario);

    if ($stmt->execute()) {
        header("Location: ../../views/usuarios.php?sucesso=Senha resetada com sucesso para 'senaisp'!");
    } else {
        header("Location: ../../views/usuarios.php?erro=erro_banco");
    }
} else {
    header("Location: ../../views/usuarios.php?erro=id_invalido");
}
