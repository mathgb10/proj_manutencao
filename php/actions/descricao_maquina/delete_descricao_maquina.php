<?php
require_once __DIR__ . "/../../configs/conexao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $acao = isset($_POST['acao']) ? $_POST['acao'] : 'deletar';

    if ($acao == 'desativar') {
        $sql = "UPDATE tipomaquina SET tipomaquina_status = 'Inativo' WHERE idtipomaquina = ?";
    } else if ($acao == 'ativar') {
        $sql = "UPDATE tipomaquina SET tipomaquina_status = 'Ativo' WHERE idtipomaquina = ?";
    } else {
        $sql = "DELETE FROM tipomaquina WHERE idtipomaquina = ?";
    }

    $stmt = mysqli_prepare($conn_nr12, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'message' => 'Descrição de máquina ' . ($acao == 'desativar' ? 'desativada' : 'excluída') . ' com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao processar: ' . mysqli_error($conn_nr12)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
}

