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

$os_id               = intval($input['os_id'] ?? 0);
$novo_responsavel_id = intval($input['responsavel_id'] ?? 0);
$motivo              = trim($input['motivo'] ?? '');
$usuario_id          = $_SESSION['user_id'] ?? 0;
$usuario_nome        = $_SESSION['user_nome'] ?? 'Desconhecido';

if ($os_id <= 0 || $novo_responsavel_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos. Informe ID da O.S. e novo responsável']);
    exit;
}

if (empty($motivo)) {
    echo json_encode(['success' => false, 'message' => 'Informe o motivo do encaminhamento']);
    exit;
}

// Buscar OS atual
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

// Apenas o responsável atual pode encaminhar (ou ADMIN/GESTOR)
$permissao = $_SESSION['user_permissao'] ?? 'NORMAL';
if ($os['responsavel_id'] != $usuario_id && $permissao !== 'ADMIN' && $permissao !== 'GESTOR') {
    echo json_encode(['success' => false, 'message' => 'Apenas o responsável atual pode encaminhar esta O.S.']);
    exit;
}

// Buscar nome do novo responsável
$sqlResp = "SELECT nome FROM usuarios WHERE id = ?";
$stmtResp = $conn->prepare($sqlResp);
$stmtResp->bind_param("i", $novo_responsavel_id);
$stmtResp->execute();
$resResp = $stmtResp->get_result();

if ($resResp->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Responsável destino não encontrado']);
    exit;
}

$novo_resp_nome = $resResp->fetch_assoc()['nome'];

// Atualizar OS: salvar quem encaminhou como anterior_responsavel_id
$sqlUpdate = "UPDATE ordens_servico SET status = 'Aguardando Aprovação', responsavel_id = ?, anterior_responsavel_id = ? WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("iii", $novo_responsavel_id, $usuario_id, $os_id);

if ($stmtUpdate->execute()) {
    $desc_hist  = "O.S. encaminhada por $usuario_nome para $novo_resp_nome em " . date('d/m/Y H:i') . ". Motivo: $motivo";
    $status_hist = "OS Encaminhada";

    $sqlHist  = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmtHist = $conn->prepare($sqlHist);
    if ($stmtHist) {
        $stmtHist->bind_param("isiss", $os_id, $status_hist, $usuario_id, $novo_responsavel_id, $desc_hist);
        $stmtHist->execute();
    }

    echo json_encode(['success' => true, 'message' => "O.S. encaminhada para $novo_resp_nome com sucesso!"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao encaminhar O.S.: ' . $conn->error]);
}
?>