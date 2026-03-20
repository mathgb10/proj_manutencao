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

$os_id    = intval($input['os_id'] ?? 0);
$motivo   = trim($input['motivo_recusa'] ?? '');
$usuario_id   = $_SESSION['user_id'] ?? 0;
$usuario_nome = $_SESSION['user_nome'] ?? 'Desconhecido';

if ($os_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da O.S. inválido']);
    exit;
}

if (empty($motivo)) {
    echo json_encode(['success' => false, 'message' => 'Informe o motivo da recusa']);
    exit;
}

// Buscar dados atuais da OS
$sqlOS = "SELECT os.*, sol.nome AS solicitante_nome, resp.nome AS responsavel_nome
          FROM ordens_servico os
          INNER JOIN usuarios sol ON os.solicitante_id = sol.id
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

// Somente o responsável atual pode recusar
if ($os['responsavel_id'] != $usuario_id) {
    echo json_encode(['success' => false, 'message' => 'Apenas o responsável atual pode recusar esta O.S.']);
    exit;
}

// Determinar para quem volta: anterior_responsavel_id ou solicitante como fallback
$volta_para_id = intval($os['anterior_responsavel_id'] ?? $os['solicitante_id']);

// Buscar nome de quem vai receber de volta
$sqlPrev = "SELECT nome FROM usuarios WHERE id = ?";
$stmtPrev = $conn->prepare($sqlPrev);
$stmtPrev->bind_param("i", $volta_para_id);
$stmtPrev->execute();
$resPrev = $stmtPrev->get_result();
$prev_nome = ($resPrev->num_rows > 0) ? $resPrev->fetch_assoc()['nome'] : 'Anterior';

// Atualizar OS: volta o responsável ao anterior, status = Em Aberto, anterior_responsavel_id = quem recusou (para cadeia)
$sqlUpdate = "UPDATE ordens_servico SET status = 'Em Aberto', responsavel_id = ?, anterior_responsavel_id = ? WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("iii", $volta_para_id, $usuario_id, $os_id);

if ($stmtUpdate->execute()) {
    // Registrar no histórico
    $desc_hist  = "O.S. recusada por $usuario_nome e devolvida a $prev_nome. Motivo: $motivo. Data: " . date('d/m/Y H:i');
    $status_hist = "OS Recusada";

    $sqlHist  = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmtHist = $conn->prepare($sqlHist);
    if ($stmtHist) {
        $stmtHist->bind_param("isiss", $os_id, $status_hist, $usuario_id, $volta_para_id, $desc_hist);
        $stmtHist->execute();
    }

    echo json_encode(['success' => true, 'message' => "O.S. recusada e devolvida para $prev_nome."]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao recusar O.S.: ' . $conn->error]);
}
?>
