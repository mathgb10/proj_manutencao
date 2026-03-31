<?php
require __DIR__ . "/../../configs/conexao.php";

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = 'senaisp';
$permissao = $_POST['permissao'];
$senha_padrao_valor = 1;

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
        nome, email, senha, permissao, senha_padrao 
    )VALUES( ?,?,?,?,?)";

    $stmt = mysqli_prepare($conn, $insert);
    $stmt->bind_param("ssssi", $nome, $email, $senha_cript, $permissao, $senha_padrao_valor);
    $stmt->execute();

    salvarLog($conn, "INSERT INTO usuarios(nome, email, senha, permissao, senha_padrao) VALUES( '$nome','$email','senaisp','$permissao', '1')");

    header("Location: ../../views/usuarios.php?sucesso=Usuário cadastrado com sucesso!");
}

