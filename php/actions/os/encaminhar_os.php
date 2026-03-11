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
require '../../configs/conexao.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$os_id = intval($input['os_id'] ?? 0);
$novo_responsavel_id = intval($input['responsavel_id'] ?? 0);
$motivo = trim($input['motivo'] ?? '');
$usuario_id = $_SESSION['user_id'] ?? 0;
$usuario_nome = $_SESSION['user_nome'] ?? 'Desconhecido';

if ($os_id <= 0 || $novo_responsavel_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos. Informe o ID da O.S. e o novo responsável']);
    exit;
}

if (empty($motivo)) {
    echo json_encode(['success' => false, 'message' => 'Informe o motivo do encaminhamento']);
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

// Atualizar O.S.
$sqlUpdate = "UPDATE ordens_servico SET status = 'Aguardando Aprovação', responsavel_id = ? WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("ii", $novo_responsavel_id, $os_id);

if ($stmtUpdate->execute()) {
    // Registrar no histórico
    try {
        $desc_hist = "Ordem de Serviço Encaminhada por $usuario_nome para $novo_resp_nome dia " . date('d/m/Y H:i') . ". Motivo: $motivo";
        $status_hist = "OS Encaminhada";

        $sqlHist = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
        $stmtHist = $conn->prepare($sqlHist);
        if ($stmtHist) {
            $stmtHist->bind_param("isiis", $os_id, $status_hist, $usuario_id, $novo_responsavel_id, $desc_hist);
            $stmtHist->execute();
        }
    } catch (Throwable $t) {}

    try {
        if (function_exists('salvarLog')) {
            salvarLog($conn, "UPDATE ordens_servico ID=$os_id ENCAMINHADA para usuario ID=$novo_responsavel_id por usuario ID=$usuario_id");
        }
    } catch (Throwable $t) {}

    echo json_encode(['success' => true, 'message' => 'O.S. encaminhada com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao encaminhar O.S.: ' . $conn->error]);
}
?>