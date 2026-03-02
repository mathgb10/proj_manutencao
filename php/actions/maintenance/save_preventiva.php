<?php
session_start();
require '../../configs/conexao.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$maquina_id = intval($input['maquina_id']);
$observacoes = $conn->real_escape_string($input['observacoes'] ?? '');
$itens_checados = $input['itens'] ?? []; // Array de IDs dos itens checkados
$usuario_id = $_SESSION['user_id'] ?? 1; // Fallback se não tiver sessão, ideal ter

// 1. Criar registro no Histórico Geral
$sql = "INSERT INTO historico_manutencao (maquina_id, usuario_id, observacoes, data_realizada) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $maquina_id, $usuario_id, $observacoes);

if ($stmt->execute()) {
    $historico_id = $stmt->insert_id;

    // 2. Registrar itens individuais
    if (!empty($itens_checados)) {
        $sqlItem = "INSERT INTO historico_itens (historico_id, item_checklist_id, status) VALUES (?, ?, 'conforme')";
        $stmtItem = $conn->prepare($sqlItem);

        foreach ($itens_checados as $itemId) {
            $itemId = intval($itemId);
            $stmtItem->bind_param("ii", $historico_id, $itemId);
            $stmtItem->execute();
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar histórico: ' . $conn->error]);
}
?>