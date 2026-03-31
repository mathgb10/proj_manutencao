<?php
require __DIR__ . '/../../configs/conexao.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID da máquina não fornecido.']);
    exit;
}

$id_maquina = intval($_GET['id']);

// Busca o histórico detalhado por item
$sql = "SELECT ci.item_verificacao, u.nome as usuario_nome, h.data_realizada, h.observacoes
        FROM historico_itens hi
        JOIN historico_manutencao h ON hi.historico_id = h.id
        JOIN checklist_itens ci ON hi.item_checklist_id = ci.id
        LEFT JOIN usuarios u ON h.usuario_id = u.id
        WHERE h.maquina_id = ?
        ORDER BY h.data_realizada DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_maquina);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
while ($row = $result->fetch_assoc()) {
    // Formata a data para exibir bonito
    $row['data_formatada'] = date('d/m/Y H:i', strtotime($row['data_realizada']));
    $history[] = $row;
}

// Busca informações da máquina para exibir no título do modal
$sql_maq = "SELECT denominacao, modelo FROM maquinas WHERE id = ?";
$stmt_maq = $conn->prepare($sql_maq);
$stmt_maq->bind_param("i", $id_maquina);
$stmt_maq->execute();
$res_maq = $stmt_maq->get_result();
$maquina_info = $res_maq->fetch_assoc();

echo json_encode([
    'success' => true,
    'history' => $history,
    'machine' => $maquina_info
]);
?>
