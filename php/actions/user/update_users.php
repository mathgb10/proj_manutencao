<?php
require "../../configs/conexao.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'] ?? "";
    $email = $_POST['email'] ?? "";
    $permissao = $_POST['permissao'] ?? "";
    $senha = $_POST['senha'] ?? "";

    $senha_criptografada = password_hash($senha, PASSWORD_BCRYPT);


    $coletando_db = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($conn, $coletando_db);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $convertendo = mysqli_fetch_array($resultado);

    if (password_verify($senha, $convertendo['senha'])) {
        $senha = $convertendo['senha'];
    } else {
        $senha = $senha_criptografada;
    }

    $nome == ""  ? $nome = $convertendo['nome'] : null;
    $email == ""  ? $email = $convertendo['email'] : null;
    $senha == ""  ? $senha = $convertendo['senha'] : null;
    $permissao == "semValor" ? $permissao = $convertendo['permissao'] : null;

    $query = "UPDATE usuarios SET nome = ?, email = ?, senha = ?, permissao = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    $stmt->bind_param("ssssi", $nome, $email, $senha, $permissao, $id);

    if ($stmt->execute()) {
        salvarLog($conn, "UPDATE usuarios SET nome = '$nome', email = '$email', permissao = '$permissao' WHERE id = '$id'");
        header("Location: ../../views/usuarios.php?update=success");
    } else {
        header("Location: ../../views/usuarios.php?update=error");
    }
}
