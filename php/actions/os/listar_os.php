<?php
// Suprimir warnings/notices que quebram JSON
error_reporting(0);
ini_set('display_errors', 0);

session_start();
require '../../configs/conexao.php';

header('Content-Type: application/json');

// Validar conexão
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco', 'dados' => [], 'contadores' => ['em_aberto' => 0, 'arquivada' => 0, 'aguardando' => 0]]);
    exit;
}

// Validar sessão para privacidade
$usuario_logado_id = $_SESSION['user_id'] ?? 0;

$status_filtro = isset($_GET['status']) ? trim($_GET['status']) : '';
$busca = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = "WHERE 1=1";
$params = [];
$types = "";

// REGRA DE PRIVACIDADE: 
// Se a OS estiver em "Aguardando Aprovação", ela só deve aparecer para o solicitante ou para o responsável.
// Outros status (Em Aberto, Aceita, Arquivada) continuam visíveis para todos.
$where .= " AND (os.status != 'Aguardando Aprovação' OR os.solicitante_id = ? OR os.responsavel_id = ?)";
$params[] = $usuario_logado_id;
$params[] = $usuario_logado_id;
$types .= "ii";

// Filtro por status
if (!empty($status_filtro)) {
    if ($status_filtro === 'Em Aberto') {
        // Na aba "Em Aberto", mostramos tanto as abertas quanto as já aceitas
        $where .= " AND (os.status = 'Em Aberto' OR os.status = 'Aceita')";
    } else {
        $where .= " AND os.status = ?";
        $params[] = $status_filtro;
        $types .= "s";
    }
}

// Filtro de busca
if (!empty($busca)) {
    $where .= " AND (os.descricao LIKE ? OR sol.nome LIKE ? OR resp.nome LIKE ?)";
    $termo = "%$busca%";
    $params[] = $termo;
    $params[] = $termo;
    $params[] = $termo;
    $types .= "sss";
}

$sql = "SELECT 
            os.id,
            os.descricao,
            os.tipo,
            os.status,
            os.criado_em,
            os.atualizado_em,
            os.solicitante_id,
            os.responsavel_id,
            sol.nome AS solicitante_nome,
            resp.nome AS responsavel_nome
        FROM ordens_servico os
        INNER JOIN usuarios sol ON os.solicitante_id = sol.id
        INNER JOIN usuarios resp ON os.responsavel_id = resp.id
        $where
        ORDER BY os.criado_em DESC";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Erro SQL: ' . $conn->error, 'dados' => [], 'contadores' => ['em_aberto' => 0, 'arquivada' => 0, 'aguardando' => 0, 'aceita' => 0]]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

$ordens = [];
while ($linha = $resultado->fetch_assoc()) {
    $ordens[] = $linha;
}

// Contadores por status (considerando a privacidade também nos números)
$sqlContadores = "SELECT 
    SUM(CASE WHEN (status = 'Em Aberto') AND (status != 'Aguardando Aprovação' OR solicitante_id = $usuario_logado_id OR responsavel_id = $usuario_logado_id) THEN 1 ELSE 0 END) AS em_aberto,
    SUM(CASE WHEN (status = 'Arquivada') AND (status != 'Aguardando Aprovação' OR solicitante_id = $usuario_logado_id OR responsavel_id = $usuario_logado_id) THEN 1 ELSE 0 END) AS arquivada,
    SUM(CASE WHEN (status = 'Aguardando Aprovação') AND (solicitante_id = $usuario_logado_id OR responsavel_id = $usuario_logado_id) THEN 1 ELSE 0 END) AS aguardando,
    SUM(CASE WHEN (status = 'Aceita') AND (status != 'Aguardando Aprovação' OR solicitante_id = $usuario_logado_id OR responsavel_id = $usuario_logado_id) THEN 1 ELSE 0 END) AS aceita
FROM ordens_servico";

$resContadores = $conn->query($sqlContadores);
$contadores = ['em_aberto' => 0, 'arquivada' => 0, 'aguardando' => 0, 'aceita' => 0];
if ($resContadores && $row = $resContadores->fetch_assoc()) {
    $contadores = [
        'em_aberto' => intval($row['em_aberto'] ?? 0),
        'arquivada' => intval($row['arquivada'] ?? 0),
        'aguardando' => intval($row['aguardando'] ?? 0),
        'aceita' => intval($row['aceita'] ?? 0)
    ];
}

echo json_encode([
    'success' => true,
    'dados' => $ordens,
    'contadores' => $contadores
]);
?>