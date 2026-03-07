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

$status_filtro = isset($_GET['status']) ? trim($_GET['status']) : '';
$busca = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = "WHERE 1=1";
$params = [];
$types = "";

// Filtro por status
if (!empty($status_filtro)) {
    $where .= " AND os.status = ?";
    $params[] = $status_filtro;
    $types .= "s";
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
    echo json_encode(['success' => false, 'message' => 'Erro SQL: ' . $conn->error, 'dados' => [], 'contadores' => ['em_aberto' => 0, 'arquivada' => 0, 'aguardando' => 0]]);
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

// Contadores por status
$sqlContadores = "SELECT 
    SUM(CASE WHEN status = 'Em Aberto' THEN 1 ELSE 0 END) AS em_aberto,
    SUM(CASE WHEN status = 'Arquivada' THEN 1 ELSE 0 END) AS arquivada,
    SUM(CASE WHEN status = 'Aguardando Aprovação' THEN 1 ELSE 0 END) AS aguardando
FROM ordens_servico";

$resContadores = $conn->query($sqlContadores);
$contadores = ['em_aberto' => 0, 'arquivada' => 0, 'aguardando' => 0];
if ($resContadores && $row = $resContadores->fetch_assoc()) {
    $contadores = [
        'em_aberto' => intval($row['em_aberto'] ?? 0),
        'arquivada' => intval($row['arquivada'] ?? 0),
        'aguardando' => intval($row['aguardando'] ?? 0)
    ];
}

echo json_encode([
    'success' => true,
    'dados' => $ordens,
    'contadores' => $contadores
]);
?>