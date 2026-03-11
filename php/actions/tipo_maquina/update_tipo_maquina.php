<?php
require_once "../../configs/conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['idtipomaquina']);
    $nome = $_POST['tipomaquina_nome'];
    $arquivo = $_POST['tipomaquina_arquivo'];
    $status = $_POST['tipomaquina_status'];

    $sql = "UPDATE tipomaquina SET tipomaquina_nome = ?, tipomaquina_arquivo = ?, tipomaquina_status = ? WHERE idtipomaquina = ?";
    $stmt = mysqli_prepare($conn_nr12, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $nome, $arquivo, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../../views/tipo_maquina.php?sucesso=Tipo de máquina atualizado com sucesso!");
    } else {
        echo "Erro ao atualizar tipo de máquina: " . mysqli_error($conn_nr12);
    }
    mysqli_stmt_close($stmt);
}
