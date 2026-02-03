<?php
require "../../configs/conexao.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $permissao = $_POST['permissao'];

    $query = "UPDATE usuarios SET nome = ?, email = ?, permissao = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    $stmt->bind_param("sssi", $nome, $email, $permissao, $id);

    if ($stmt->execute()) {
        salvarLog($conn, "UPDATE usuarios SET nome = '$nome', email = '$email', permissao = '$permissao' WHERE id = '$id'");
        header("Location: ../../views/usuarios.php?update=success");
    } else {
        header("Location: ../../views/usuarios.php?update=error");
    }
}
?>