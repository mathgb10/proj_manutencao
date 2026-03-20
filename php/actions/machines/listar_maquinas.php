<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Sessão não iniciada.']);
    exit;
}
require '../../configs/conexao.php';

header('Content-Type: application/json');

$search = trim($_GET['search'] ?? '');

if (strlen($search) < 2) {
    echo json_encode(['success' => false, 'dados' => []]);
    exit;
}

// Buscar em maquinas + motores (tabelas comuns do sistema)
// Tenta buscar por nome ou patrimônio em qualquer tabela disponível
$dados = [];

// Buscar em maquinas
$sqlM = "SELECT id, nome, patrimonio FROM maquinas WHERE nome LIKE ? OR patrimonio LIKE ? ORDER BY nome ASC LIMIT 10";
$stmtM = $conn->prepare($sqlM);
if ($stmtM) {
    $termo = "%$search%";
    $stmtM->bind_param("ss", $termo, $termo);
    $stmtM->execute();
    $resM = $stmtM->get_result();
    while ($row = $resM->fetch_assoc()) {
        $dados[] = [
            'id'         => $row['id'],
            'nome'       => $row['nome'],
            'patrimonio' => $row['patrimonio'] ?? $row['id'],
            'tipo'       => 'maquina'
        ];
    }
}

// Buscar em motores (se existir)
$sqlMot = "SELECT id, nome, patrimonio FROM motores WHERE nome LIKE ? OR patrimonio LIKE ? ORDER BY nome ASC LIMIT 5";
$stmtMot = $conn->prepare($sqlMot);
if ($stmtMot) {
    $termo = "%$search%";
    $stmtMot->bind_param("ss", $termo, $termo);
    $stmtMot->execute();
    $resMot = $stmtMot->get_result();
    while ($row = $resMot->fetch_assoc()) {
        $dados[] = [
            'id'         => $row['id'],
            'nome'       => $row['nome'],
            'patrimonio' => $row['patrimonio'] ?? $row['id'],
            'tipo'       => 'motor'
        ];
    }
}

echo json_encode(['success' => true, 'dados' => $dados]);
?>
