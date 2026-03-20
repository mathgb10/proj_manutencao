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

$os_id = intval($_GET['id'] ?? 0);

if ($os_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID da O.S. inválido']);
    exit;
}

// Dados da OS
$sql = "SELECT 
            os.id,
            os.descricao,
            os.tipo,
            os.patrimonio,
            os.status,
            os.gasto,
            os.obs_finalizacao,
            os.criado_em,
            os.atualizado_em,
            os.solicitante_id,
            os.responsavel_id,
            os.anterior_responsavel_id,
            sol.nome AS solicitante_nome,
            resp.nome AS responsavel_nome
        FROM ordens_servico os
        INNER JOIN usuarios sol ON os.solicitante_id = sol.id
        INNER JOIN usuarios resp ON os.responsavel_id = resp.id
        WHERE os.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $os_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Ordem de Serviço não encontrada']);
    exit;
}

$os = $resultado->fetch_assoc();

// Buscar anexos
$sqlAnexos = "SELECT id, nome_arquivo, caminho, criado_em FROM os_anexos WHERE os_id = ? ORDER BY criado_em DESC";
$stmtAnexos = $conn->prepare($sqlAnexos);
$stmtAnexos->bind_param("i", $os_id);
$stmtAnexos->execute();
$resAnexos = $stmtAnexos->get_result();

$anexos = [];
while ($anexo = $resAnexos->fetch_assoc()) {
    $anexos[] = $anexo;
}

$os['anexos'] = $anexos;

echo json_encode(['success' => true, 'dados' => $os]);
?>