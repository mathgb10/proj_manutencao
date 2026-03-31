<?php
require_once __DIR__ . "/../../configs/conexao.php";

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT id, nome, email, permissao FROM usuarios WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Usuário não encontrado']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['error' => 'ID não fornecido']);
}

