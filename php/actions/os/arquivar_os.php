<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit;
}
require '../../configs/conexao.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$os_id            = intval($input['os_id'] ?? 0);
$gasto            = isset($input['gasto']) && $input['gasto'] !== '' ? floatval($input['gasto']) : null;
$obs_finalizacao  = trim($input['obs_finalizacao'] ?? '');
$usuario_id       = $_SESSION['user_id'] ?? 0;
$usuario_nome     = $_SESSION['user_nome'] ?? 'Desconhecido';

if ($os_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da O.S. inválido']);
    exit;
}

// Buscar dados atuais da OS
$sqlOS = "SELECT * FROM ordens_servico WHERE id = ?";
$stmtOS = $conn->prepare($sqlOS);
$stmtOS->bind_param("i", $os_id);
$stmtOS->execute();
$resOS = $stmtOS->get_result();

if ($resOS->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'O.S. não encontrada']);
    exit;
}

$os = $resOS->fetch_assoc();

// Apenas responsável atual pode arquivar (ou ADMIN)
$permissao = $_SESSION['user_permissao'] ?? 'NORMAL';
if ($os['responsavel_id'] != $usuario_id && $permissao !== 'ADMIN' && $permissao !== 'GESTOR') {
    echo json_encode(['success' => false, 'message' => 'Apenas o responsável atual pode arquivar esta O.S.']);
    exit;
}

// Atualizar status e salvar gasto + observação
$sqlUpdate = "UPDATE ordens_servico SET status = 'Arquivada', gasto = ?, obs_finalizacao = ? WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("dsi", $gasto, $obs_finalizacao, $os_id);

if ($stmtUpdate->execute()) {
    $gastoFormatado = $gasto !== null ? 'R$ ' . number_format($gasto, 2, ',', '.') : 'Não informado';
    $desc_hist  = "O.S. arquivada (finalizada) por $usuario_nome em " . date('d/m/Y H:i') . ". Gasto: $gastoFormatado." . (!empty($obs_finalizacao) ? " Obs: $obs_finalizacao" : '');
    $status_hist = "OS Arquivada";

    $sqlHist  = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmtHist = $conn->prepare($sqlHist);
    if ($stmtHist) {
        $stmtHist->bind_param("isiss", $os_id, $status_hist, $usuario_id, $os['solicitante_id'], $desc_hist);
        $stmtHist->execute();
    }

    echo json_encode(['success' => true, 'message' => 'O.S. arquivada (finalizada) com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao arquivar O.S.: ' . $conn->error]);
}
?>