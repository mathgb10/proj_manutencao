<?php
require "../configs/conexao.php";

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$permissao = $_POST['permissao'];

$coletando_db = "SELECT * FROM usuarios WHERE email = ?";
$stmt = mysqli_prepare($conn, $coletando_db);
$stmt->bind_param("s", $email);
$stmt->execute();
$result->get_result();
$row = mysqli_num_rows($result);

if ($row > 0) {
    header("Location: ../views/usuarios.php?deu_certo=false");
} else {
    $insert = "INSERT INTO usuarios(
        nome, email, senha, permissao 
    )VALUES( ?,?,?,?)";

    $stmt = mysqli_prepare($conn, $insert);
    $stmt->bind_param("ssss", $nome, $email, $senha, $permissao);
    $stmt->execute();

    header("Location: ../views/usuarios.php?deu_certo=true");
}

?>