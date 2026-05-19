<?php
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

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$os_id       = intval($input['os_id'] ?? 0);
$usuario_id  = $_SESSION['user_id'] ?? 0;
$usuario_nome = $_SESSION['user_nome'] ?? 'Desconhecido';

if ($os_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da O.S. inválido']);
    exit;
}

// Buscar dados atuais da OS
$sqlOS = "SELECT os.*, resp.nome AS responsavel_nome FROM ordens_servico os
          INNER JOIN usuarios resp ON os.responsavel_id = resp.id
          WHERE os.id = ?";
$stmtOS = $conn->prepare($sqlOS);
$stmtOS->bind_param("i", $os_id);
$stmtOS->execute();
$resOS = $stmtOS->get_result();

if ($resOS->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'O.S. não encontrada']);
    exit;
}

$os = $resOS->fetch_assoc();

// Apenas o responsável atual pode aceitar (ou qualquer um se estiver Em Aberto, ou ADMIN/GESTOR)
$permissao = $_SESSION['user_permissao'] ?? 'NORMAL';
if ($os['status'] !== 'Em Aberto' && $os['responsavel_id'] != $usuario_id && $permissao !== 'ADMIN' && $permissao !== 'GESTOR') {
    echo json_encode(['success' => false, 'message' => 'Apenas o responsável designado pode aceitar esta O.S.']);
    exit;
}

// Atualizar status para Aceita, definindo o usuário atual como responsável e guardando o anterior
$sqlUpdate = "UPDATE ordens_servico SET status = 'Aceita', responsavel_id = ?, anterior_responsavel_id = responsavel_id WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("ii", $usuario_id, $os_id);

if ($stmtUpdate->execute()) {
    // Registrar no histórico
    $desc_hist  = "O.S. aceita por $usuario_nome em " . date('d/m/Y H:i') . '.';
    $status_hist = "OS Aceita";

    $sqlHist  = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmtHist = $conn->prepare($sqlHist);
    if ($stmtHist) {
        $stmtHist->bind_param("isiss", $os_id, $status_hist, $os['responsavel_id'], $usuario_id, $desc_hist);
        $stmtHist->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Ordem de Serviço aceita com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao aceitar O.S.: ' . $conn->error]);
}
?>
