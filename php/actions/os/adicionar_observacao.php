<?php
include_once '../../config/conexao.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$os_id      = $data['os_id'] ?? null;
$observacao = $data['observacao'] ?? '';
$usuario_id = $_SESSION['user_id'] ?? null;

if (!$os_id || empty($observacao) || !$usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Dados incompletos!']);
    exit;
}

// Buscar o responsável atual para manter no histórico
$sqlOS = "SELECT responsavel_id FROM ordens_servico WHERE id = ?";
$stmtOS = $conn->prepare($sqlOS);
$stmtOS->bind_param("i", $os_id);
$stmtOS->execute();
$resOS = $stmtOS->get_result();
$os = $resOS->fetch_assoc();

if (!$os) {
    echo json_encode(['success' => false, 'message' => 'O.S. não encontrada!']);
    exit;
}

$responsavel_id = $os['responsavel_id'];

// Inserir no histórico como Observação
$sqlHist = "INSERT INTO os_historico (os_id, origem_id, destino_id, status, descricao) 
            VALUES (?, ?, ?, 'Observação', ?)";
$stmtHist = $conn->prepare($sqlHist);
$stmtHist->bind_param("iiis", $os_id, $usuario_id, $responsavel_id, $observacao);

if ($stmtHist->execute()) {
    echo json_encode(['success' => true, 'message' => 'Observação adicionada com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar: ' . $conn->error]);
}
?>
