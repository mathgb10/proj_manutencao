<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'dados' => []]);
    exit;
}
require '../../configs/conexao.php';

header('Content-Type: application/json');

$search = trim($_GET['search'] ?? '');
$dados  = [];

// Se search vazio → lista tudo (limite 50). Se tem texto → filtra.
if ($search === '') {
    $sqlM = "SELECT id, denominacao, numero_identificacao, numero_serie, setor
             FROM maquinas
             ORDER BY denominacao ASC LIMIT 50";
    $stmtM = $conn->prepare($sqlM);
} else {
    $termo = "%$search%";
    $sqlM  = "SELECT id, denominacao, numero_identificacao, numero_serie, setor
              FROM maquinas
              WHERE denominacao LIKE ? OR numero_identificacao LIKE ? OR numero_serie LIKE ?
              ORDER BY denominacao ASC LIMIT 20";
    $stmtM = $conn->prepare($sqlM);
    if ($stmtM) {
        $stmtM->bind_param("sss", $termo, $termo, $termo);
    }
}

if ($stmtM) {
    $stmtM->execute();
    $resM = $stmtM->get_result();
    while ($row = $resM->fetch_assoc()) {
        $dados[] = [
            'id'         => $row['id'],
            'nome'       => $row['denominacao'],
            'patrimonio' => $row['numero_identificacao'] ?: ($row['numero_serie'] ?: (string)$row['id']),
            'setor'      => $row['setor'] ?? '',
            'tipo'       => 'maquina'
        ];
    }
    $stmtM->close();
}

echo json_encode(['success' => true, 'dados' => $dados]);
?>
