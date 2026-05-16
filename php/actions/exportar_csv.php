<?php
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';

$tipo = $_GET['tipo'] ?? 'preventiva';
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=relatorio_' . $tipo . '_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
// UTF-8 BOM para o Excel abrir corretamente
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

if ($tipo == 'preventiva') {
    fputcsv($output, ['NI', 'Denominação', 'Marca', 'Modelo', 'Fabricante', 'Setor']);
    $sql = "SELECT * FROM maquinas ORDER BY denominacao ASC";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        if ($busca_atual && strpos(strtolower($row['denominacao']), strtolower($busca_atual)) === false) continue;
        fputcsv($output, [
            $row['numero_identificacao'],
            $row['denominacao'],
            $row['marca'],
            $row['modelo'],
            $row['fabricante'],
            $row['setor']
        ]);
    }
} else {
    fputcsv($output, ['Nº O.S.', 'Patrimônio', 'Descrição', 'Tipo', 'Data Criação', 'Solicitante', 'Responsável', 'Status']);
    $sql = "SELECT os.*, sol.nome as sol_nome, resp.nome as resp_nome 
            FROM ordens_servico os 
            INNER JOIN usuarios sol ON os.solicitante_id = sol.id 
            INNER JOIN usuarios resp ON os.responsavel_id = resp.id 
            WHERE os.tipo = 'Corretivo' 
            ORDER BY os.id DESC";
    $res = $conn->query($sql);
    while ($row = $res->fetch_assoc()) {
        if ($busca_atual && strpos(strtolower($row['descricao']), strtolower($busca_atual)) === false) continue;
        fputcsv($output, [
            $row['id'],
            $row['patrimonio'],
            $row['descricao'],
            $row['tipo'],
            date('d/m/Y H:i', strtotime($row['criado_em'])),
            $row['sol_nome'],
            $row['resp_nome'],
            $row['status']
        ]);
    }
}

fclose($output);
exit;
