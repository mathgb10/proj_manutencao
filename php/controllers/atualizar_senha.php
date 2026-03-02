<?php
session_start();
require '../configs/conexao.php'; // Ajuste o caminho conforme sua estrutura

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recebe os dados
    $id_usuario = $_PT['id'];
    $nova_senha = $_POST['nova_senha'];

    // Validação básica
    if (empty($id_usuario) || empty($nova_senha)) {
        header("Location: ../pages/usuarios.php?msg=erro_vazio");
        exit;
    }

    // Criptografa a senha (IMPORTANTE: Nunca salve senhas em texto puro)
    // Se seu sistema usa md5 (antigo), use md5($nova_senha). 
    // O recomendado hoje é password_hash.
    $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
    // OU se seu banco usa texto puro (não recomendado): $senha_hash = $nova_senha;

    // Prepara a query SQL
    $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $senha_hash, $id_usuario);

        if ($stmt->execute()) {
            // Sucesso
            header("Location: ../pages/usuarios.php?msg=senha_atualizada");
        } else {
            // Erro no SQL
            header("Location: ../pages/usuarios.php?msg=erro_sql");
        }
        $stmt->close();
    } else {
        header("Location: ../pages/usuarios.php?msg=erro_conexao");
    }

    $conn->close();
} else {
    // Se tentar acessar o arquivo diretamente sem post
    header("Location: ../pages/usuarios.php");
}
