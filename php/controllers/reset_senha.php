<?php
// controllers/reset_senha.php
session_start();
require __DIR__ . '/../configs/conexao.php'; // Certifique-se que este caminho está correto

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $senha_bruta = $_POST['senha'];

    // Validações básicas
    if (empty($id) || empty($senha_bruta)) {
        header("Location: ../pages/usuarios.php?msg=erro_dados");
        exit;
    }

    // CRIPTOGRAFIA (Hash)
    // Como seu banco é VARCHAR(245), podemos usar PASSWORD_DEFAULT tranquilamente.
    // Isso gera um hash seguro, nunca salvamos senha em texto puro.
    $senha_hash = password_hash($senha_bruta, PASSWORD_DEFAULT);

    // SQL UPDATE
    $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // "si" significa String (senha), Integer (id)
        $stmt->bind_param("si", $senha_hash, $id);

        if ($stmt->execute()) {
            // Sucesso
            header("Location: ../pages/usuarios.php?msg=senha_sucesso");
        } else {
            // Erro na execução
            header("Location: ../pages/usuarios.php?msg=erro_sql");
        }

        $stmt->close();
    } else {
        // Erro na preparação
        header("Location: ../pages/usuarios.php?msg=erro_conexao");
    }

    $conn->close();
} else {
    header("Location: ../pages/usuarios.php");
    exit;
}

