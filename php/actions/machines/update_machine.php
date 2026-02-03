<?php
session_start();
require_once '../../configs/conexao.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Aceita tanto 'id_maquina' quanto 'id' para compatibilidade com formulários
    $id = isset($_POST['id_maquina']) ? intval($_POST['id_maquina']) : (isset($_POST['id']) ? intval($_POST['id']) : null);

    $denominacao = mysqli_real_escape_string($conn, $_POST['denominacao']);
    $marca = mysqli_real_escape_string($conn, $_POST['marca']);
    $modelo = mysqli_real_escape_string($conn, $_POST['modelo']);
    $numero_identificacao = mysqli_real_escape_string($conn, $_POST['numero_identificacao']);
    $numero_serie = mysqli_real_escape_string($conn, $_POST['numero_serie']);
    $ano_fabricacao = isset($_POST['ano_fabricacao']) && $_POST['ano_fabricacao'] !== '' ? intval($_POST['ano_fabricacao']) : null;
    $setor = mysqli_real_escape_string($conn, $_POST['setor']);

    $sql = "UPDATE maquinas SET
                denominacao = ?,
                marca = ?,
                modelo = ?,
                numero_identificacao = ?,
                numero_serie = ?,
                ano_fabricacao = ?,
                setor = ?
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        $stmt->bind_param("sssssisi", $denominacao, $marca, $modelo, $numero_identificacao, $numero_serie, $ano_fabricacao, $setor, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['success_message'] = "Máquina atualizada com sucesso.";
        } else {
            $_SESSION['error_message'] = "Nenhuma alteração feita ou máquina não encontrada.";
        }
    } else {
        $_SESSION['error_message'] = "Erro ao preparar a consulta: " . mysqli_error($conn);
    }

    header("Location: ../../views/maquinas.php");
    exit();
}
