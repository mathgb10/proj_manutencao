<?php
require "../../configs/conexao.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $id_usuario = $_SESSION['user_id'];

    if ($nova_senha === $confirmar_senha) {
        $senha_cript = password_hash($nova_senha, PASSWORD_DEFAULT);
        $senha_padrao = 0;

        $update = "UPDATE usuarios SET senha = ?, senha_padrao = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update);
        $stmt->bind_param("sii", $senha_cript, $senha_padrao, $id_usuario);

        if ($stmt->execute()) {
            $_SESSION['user_senha_padrao'] = 0; // Atualiza a sessão
            header("Location: ../../views/dashboard.php?msg=senha_atualizada");
        } else {
            header("Location: ../../views/dashboard.php?erro=erro_banco");
        }
    } else {
        header("Location: ../../views/dashboard.php?erro=senhas_nao_coincidem");
    }
} else {
    header("Location: ../../index.php");
}
