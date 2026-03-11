<?php
require_once "../../configs/conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fabricante = $_POST['motor_fabricante'];
    $modelo = $_POST['motor_modelo'];
    $potencia = $_POST['motor_potencia'];
    $tensao = $_POST['motor_tensao'];
    $corrente = $_POST['motor_corrente'];
    $status = 'Ativo';

    $sql = "INSERT INTO motor (motor_fabricante, motor_modelo, motor_potencia, `motor_tensão`, motor_corrente, motor_status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn_nr12, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $fabricante, $modelo, $potencia, $tensao, $corrente, $status);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../../views/motores.php?sucesso=Motor cadastrado com sucesso!");
    } else {
        echo "Erro ao cadastrar motor: " . mysqli_error($conn_nr12);
    }
    mysqli_stmt_close($stmt);
}
