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
$usuario_id = $_SESSION['user_id'] ?? 0;
$usuario_nome = $_SESSION['user_nome'] ?? 'Desconhecido';

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

// Atualizar status para Arquivada
$sqlUpdate = "UPDATE ordens_servico SET status = 'Arquivada' WHERE id = ?";
$stmtUpdate = $conn->prepare($sqlUpdate);
$stmtUpdate->bind_param("i", $os_id);

if ($stmtUpdate->execute()) {
    // Registrar no histórico
    try {
        $desc_hist = "Ordem de Serviço arquivada por $usuario_nome dia " . date('d/m/Y H:i');
        $status_hist = "OS Arquivada";

        $sqlHist = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
        $stmtHist = $conn->prepare($sqlHist);
        if ($stmtHist) {
            $stmtHist->bind_param("isiis", $os_id, $status_hist, $usuario_id, $os['responsavel_id'], $desc_hist);
            $stmtHist->execute();
        }
    } catch (Throwable $t) {}

    try {
        if (function_exists('salvarLog')) {
            salvarLog($conn, "UPDATE ordens_servico ID=$os_id STATUS=Arquivada por usuario ID=$usuario_id");
        }
    } catch (Throwable $t) {}

    echo json_encode(['success' => true, 'message' => 'O.S. arquivada com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao arquivar O.S.: ' . $conn->error]);
}
?>