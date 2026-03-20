<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error_log.txt');

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR])) {
        if (!headers_sent()) header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Erro PHP: ' . $error['message']]);
    }
});

session_start();
require '../../configs/conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$descricao    = trim($input['descricao'] ?? '');
$tipo         = trim($input['tipo'] ?? '');
$patrimonio   = trim($input['patrimonio'] ?? '');
$solicitante_id   = $_SESSION['user_id'] ?? 0;
$solicitante_nome = $_SESSION['user_nome'] ?? 'Desconhecido';

// Validações básicas
if (empty($descricao) || empty($tipo)) {
    echo json_encode(['success' => false, 'message' => 'Preencha todos os campos obrigatórios']);
    exit;
}

$tipos_validos = ['Manutenção', 'Corretivo'];
if (!in_array($tipo, $tipos_validos)) {
    echo json_encode(['success' => false, 'message' => 'Tipo inválido']);
    exit;
}

// Buscar automaticamente o primeiro GESTOR (ou ADMIN) disponível
$sqlGestor = "SELECT id, nome FROM usuarios WHERE permissao IN ('GESTOR','ADMIN') ORDER BY id ASC LIMIT 1";
$resGestor = $conn->query($sqlGestor);

if (!$resGestor || $resGestor->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Nenhum gestor cadastrado no sistema. Contate o administrador.']);
    exit;
}

$gestor = $resGestor->fetch_assoc();
$responsavel_id   = $gestor['id'];
$responsavel_nome = $gestor['nome'];

// Inserir a Ordem de Serviço — tipo padrão Corretivo, responsável = gestor
$sql = "INSERT INTO ordens_servico (descricao, tipo, patrimonio, status, solicitante_id, responsavel_id, anterior_responsavel_id)
        VALUES (?, ?, ?, 'Em Aberto', ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro ao preparar SQL: ' . $conn->error]);
    exit;
}
// anterior_responsavel_id começa como o próprio solicitante (ponto de retorno)
$stmt->bind_param("sssiii", $descricao, $tipo, $patrimonio, $solicitante_id, $responsavel_id, $solicitante_id);

if ($stmt->execute()) {
    $os_id = $stmt->insert_id;

    // Histórico de criação
    $desc_hist  = "O.S. criada por $solicitante_nome e encaminhada automaticamente para o gestor $responsavel_nome em " . date('d/m/Y H:i') . ".";
    $status_hist = "OS Criada";

    $sqlHist  = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (?, ?, ?, ?, ?)";
    $stmtHist = $conn->prepare($sqlHist);
    if ($stmtHist) {
        $stmtHist->bind_param("isiss", $os_id, $status_hist, $solicitante_id, $responsavel_id, $desc_hist);
        $stmtHist->execute();
    }

    echo json_encode(['success' => true, 'message' => 'Ordem de Serviço criada com sucesso!', 'os_id' => $os_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao criar O.S.: ' . $conn->error]);
}
?>
