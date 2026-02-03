<?php
session_start();
require_once("../../configs/conexao.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

$nome_maquina = mysqli_real_escape_string($conn, $_POST['nome_maquina']);
$ip_maquina = mysqli_real_escape_string($conn, $_POST['ip_maquina']);
$local_maquina = mysqli_real_escape_string($conn, $_POST['local_maquina']);
$status_maquina = mysqli_real_escape_string($conn, $_POST['status_maquina']);
// $data_cadastro = date('d/m/Y H:i:s'); esse aqui n precisa ja ta como DEFAULT CURRENT TIMESTAMP

$sql = "INSERT INTO maquinas (nome_maquina, ip_maquina, local_maquina, status_maquina,) 
            VALUES ('$nome_maquina', '$ip_maquina', '$local_maquina', '$status_maquina')";
$stmt = mysqli_prepare($conexao, $sql);
$stmt->bind_param("ssss", $nome_maquina, $ip_maquina, $local_maquina, $status_maquina);
$stmt->execute();
$result = $stmt->get_result();

if ($stmt->affected_rows > 0) {
    echo "<script>alert('Máquina cadastrada com sucesso!'); window.location.href='../../views/machines/register_machines.php';</script>";
} else {
    echo "<script>alert('Erro ao cadastrar a máquina. Por favor, tente novamente.'); window.location.href='../../views/machines/register_machines.php';</script>";
}
