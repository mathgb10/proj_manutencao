<?php
require_once "../../configs/conexao.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['idmotor']);
    $fabricante = $_POST['motor_fabricante'];
    $modelo = $_POST['motor_modelo'];
    $potencia = $_POST['motor_potencia'];
    $tensao = $_POST['motor_tensao'];
    $corrente = $_POST['motor_corrente'];
    $status = $_POST['motor_status'];

    $sql = "UPDATE motor SET motor_fabricante = ?, motor_modelo = ?, motor_potencia = ?, `motor_tensão` = ?, motor_corrente = ?, motor_status = ? WHERE idmotor = ?";
    $stmt = mysqli_prepare($conn_nr12, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssi", $fabricante, $modelo, $potencia, $tensao, $corrente, $status, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../../views/motores.php?sucesso=Motor atualizado com sucesso!");
    } else {
        echo "Erro ao atualizar motor: " . mysqli_error($conn_nr12);
    }
    mysqli_stmt_close($stmt);
}
