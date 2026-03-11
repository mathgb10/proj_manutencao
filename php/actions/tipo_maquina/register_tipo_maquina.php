<?php
require_once "../../configs/conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['tipomaquina_nome'];
    $arquivo = $_POST['tipomaquina_arquivo'];
    $status = 'Ativo';

    $sql = "INSERT INTO tipomaquina (tipomaquina_nome, tipomaquina_arquivo, tipomaquina_status) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn_nr12, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $nome, $arquivo, $status);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../../views/tipo_maquina.php?sucesso=Tipo de máquina cadastrado com sucesso!");
    } else {
        echo "Erro ao cadastrar tipo de máquina: " . mysqli_error($conn_nr12);
    }
    mysqli_stmt_close($stmt);
}
