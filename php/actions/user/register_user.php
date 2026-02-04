<?php
require "../../configs/conexao.php";

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = 'senaisp';
$permissao = $_POST['permissao'];

$coletando_db = "SELECT * FROM usuarios WHERE email = ?";
$stmt = mysqli_prepare($conn, $coletando_db);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->num_rows;

if ($row > 0) {
    header("Location: ../../views/usuarios.php?deu_certo=false");
} else {
    $senha_cript = password_hash($senha, PASSWORD_DEFAULT);
    $insert = "INSERT INTO usuarios(
        nome, email, senha, permissao 
    )VALUES( ?,?,?,?)";

    $stmt = mysqli_prepare($conn, $insert);
    $stmt->bind_param("ssss", $nome, $email, $senha_cript, $permissao);
    $stmt->execute();

    salvarLog($conn, "INSERT INTO usuarios(nome, email, senha, permissao) VALUES( '$nome','$email','senaisp','$permissao')");

    header("Location: ../../views/usuarios.php?deu_certo=true");
}
