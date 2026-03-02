<?php
require_once "../../configs/conexao.php";

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "SELECT * FROM maquinas WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Buscar itens do checklist
        $sql_checklist = "SELECT id, item_verificacao, frequencia FROM checklist_itens WHERE maquina_id = ?";
        $stmt_checklist = mysqli_prepare($conn, $sql_checklist);
        mysqli_stmt_bind_param($stmt_checklist, "i", $id);
        mysqli_stmt_execute($stmt_checklist);
        $result_checklist = mysqli_stmt_get_result($stmt_checklist);

        $checklist = [];
        while ($item = mysqli_fetch_assoc($result_checklist)) {
            $checklist[] = $item;
        }

        $row['checklist'] = $checklist;

        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Máquina não encontrada']);
    }
} else {
    echo json_encode(['error' => 'ID não fornecido']);
}
