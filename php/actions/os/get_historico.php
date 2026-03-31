<?php
// Suprimir warnings/notices que quebram JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit;
}
require __DIR__ . '/../../configs/conexao.php';

header('Content-Type: application/json');

$os_id = intval($_GET['os_id'] ?? 0);

if ($os_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da O.S. inválido']);
    exit;
}

$sql = "SELECT 
            h.id,
            h.status,
            h.descricao,
            h.criado_em,
            ori.nome AS origem_nome,
            dest.nome AS destino_nome
        FROM os_historico h
        INNER JOIN usuarios ori ON h.origem_id = ori.id
        INNER JOIN usuarios dest ON h.destino_id = dest.id
        WHERE h.os_id = ?
        ORDER BY h.criado_em DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $os_id);
$stmt->execute();
$resultado = $stmt->get_result();

$historico = [];
while ($linha = $resultado->fetch_assoc()) {
    $historico[] = $linha;
}

echo json_encode(['success' => true, 'dados' => $historico]);
?>
