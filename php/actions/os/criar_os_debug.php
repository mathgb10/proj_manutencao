<?php
session_start();
require '../../configs/conexao.php';

header('Content-Type: application/json');

$input = json_decode(file_get_contents('mock_input.json'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$descricao = trim($input['descricao'] ?? '');
$tipo = trim($input['tipo'] ?? '');
$responsavel_id = intval($input['responsavel_id'] ?? 0);
$solicitante_id = $_SESSION['user_id'] ?? 0;
$solicitante_nome = $_SESSION['user_nome'] ?? 'Desconhecido';

// Validações
if (empty($descricao) || empty($tipo) || $responsavel_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    exit;
}

$tipos_validos = ['Manutenção', 'Patrimônio', 'Outros'];
if (!in_array($tipo, $tipos_validos)) {
    echo json_encode(['success' => false, 'message' => 'Tipo inválido']);
    exit;
}

// 1. Inserir a Ordem de Serviço
$sql = "INSERT INTO ordens_servico (descricao, tipo, status, solicitante_id, responsavel_id) VALUES (?, ?, 'Em Aberto', ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssii", $descricao, $tipo, $solicitante_id, $responsavel_id);

if ($stmt->execute()) {
    $os_id = $stmt->insert_id;

    // 2. Buscar nome do responsável destino
    $sqlResp = "SELECT nome FROM usuarios WHERE id = ?";
    $stmtResp = $conn->prepare($sqlResp);
    $stmtResp->bind_param("i", $responsavel_id);
    $stmtResp->execute();
    $resResp = $stmtResp->get_result();
    $responsavel_nome = ($resResp->num_rows > 0) ? $resResp->fetch_assoc()['nome'] : 'Desconhecido';

    // 3. Criar primeiro registro no histórico
    $desc_hist = "Ordem de Serviço criada por $solicitante_nome em " . date('d/m/Y H:i') . " e encaminhada para $responsavel_nome. Com a seguinte Descrição: $descricao";
    $status_hist = "OS Criada";

    $sqlHist = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmtHist = $conn->prepare($sqlHist);
    $stmtHist->bind_param("isiis", $os_id, $status_hist, $solicitante_id, $responsavel_id, $desc_hist);
    $stmtHist->execute();

    salvarLog($conn, "INSERT ordens_servico ID=$os_id por usuario ID=$solicitante_id");

    echo json_encode(['success' => true, 'message' => 'Ordem de Serviço criada com sucesso!', 'os_id' => $os_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao criar O.S.: ' . $conn->error]);
}
?>
