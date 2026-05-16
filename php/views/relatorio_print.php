<?php
require __DIR__ . '/../controllers/validar_acesso.php';
require __DIR__ . '/../configs/conexao.php';

$tipo_relatorio = $_GET['tipo'] ?? 'preventiva';
$busca_atual = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_atual = isset($_GET['filtro-status']) ? $_GET['filtro-status'] : 'todos';

$titulo = ($tipo_relatorio == 'preventiva') ? 'Relatório de Manutenção Preventiva' : 'Relatório de Manutenção Corretiva';

if ($tipo_relatorio == 'preventiva') {
    // Lógica da Preventiva (Máquinas + Status calculado)
    function calcularStatusArray($lastDate, $intervalo) {
        if (!$intervalo) $intervalo = 30;
        if (!$lastDate) return ['status' => 'PENDENTE', 'class' => 'warning', 'label' => 'PENDENTE', 'data' => 'S/ REGISTRO'];
        $proxima = date('Y-m-d', strtotime($lastDate . " + $intervalo days"));
        $hoje = date('Y-m-d');
        if ($proxima < $hoje) return ['status' => 'VENCIDO', 'class' => 'danger', 'label' => 'VENCIDO', 'data' => date('d/m/Y', strtotime($proxima))];
        else return ['status' => 'OK', 'class' => 'success', 'label' => 'EM DIA', 'data' => date('d/m/Y', strtotime($proxima))];
    }

    $sql = "SELECT m.*, (SELECT MAX(data_realizada) FROM historico_manutencao WHERE maquina_id = m.id) as ultima FROM maquinas m ORDER BY m.denominacao ASC";
    $res = $conn->query($sql);
    $dados = [];
    while ($row = $res->fetch_assoc()) {
        $dataStatus = calcularStatusArray($row['ultima'], 30); // Intervalo fixo para simplificar relatorio
        if ($busca_atual && strpos(strtolower($row['denominacao']), strtolower($busca_atual)) === false) continue;
        $row['col_1'] = $row['numero_identificacao'];
        $row['col_2'] = $row['denominacao'];
        $row['col_3'] = $row['ultima'] ? date('d/m/Y', strtotime($row['ultima'])) : 'S/ Registro';
        $row['col_4'] = $dataStatus['data'];
        $row['col_5'] = $dataStatus['label'];
        $dados[] = $row;
    }
    $headers = ['NI', 'Máquina', 'Última Manut.', 'Próxima Manut.', 'Status'];

} else {
    // Lógica da Corretiva (O.S. do tipo Corretivo)
    $sql = "SELECT os.*, sol.nome as sol_nome FROM ordens_servico os INNER JOIN usuarios sol ON os.solicitante_id = sol.id WHERE os.tipo = 'Corretivo' ORDER BY os.criado_em DESC";
    $res = $conn->query($sql);
    $dados = [];
    while ($row = $res->fetch_assoc()) {
        if ($busca_atual && strpos(strtolower($row['descricao']), strtolower($busca_atual)) === false) continue;
        $row['col_1'] = "#" . $row['id'];
        $row['col_2'] = $row['patrimonio'] ?: 'N/A';
        $row['col_3'] = date('d/m/Y', strtotime($row['criado_em']));
        $row['col_4'] = $row['sol_nome'];
        $row['col_5'] = $row['status'];
        $dados[] = $row;
    }
    $headers = ['Nº O.S.', 'Patrimônio', 'Data', 'Solicitante', 'Status'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title><?= $titulo ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 30px; color: #333; line-height: 1.6; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #ed1c24; padding-bottom: 15px; margin-bottom: 25px; }
        .header img { height: 60px; }
        .header h1 { font-size: 1.8rem; margin: 0; color: #ed1c24; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 12px 8px; text-align: left; font-size: 0.95rem; }
        th { background-color: #f8f9fa; font-weight: bold; text-transform: uppercase; font-size: 0.85rem; }
        .footer { margin-top: 40px; font-size: 0.85rem; text-align: center; border-top: 1px solid #eee; padding-top: 10px; opacity: 0.7; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right; display: flex; gap: 10px; justify-content: flex-end;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #ed1c24; color: white; border: none; border-radius: 6px; font-weight: bold;">IMPRIMIR RELATÓRIO</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; border-radius: 6px; border: 1px solid #ddd;">FECHAR</button>
    </div>

    <div class="header">
        <img src="../../assets/imgs/senailogo.png" alt="SENAI">
        <div style="text-align: right;">
            <h1><?= $titulo ?></h1>
            <p style="margin-top: 5px;">Data de Geração: <strong><?= date('d/m/Y H:i') ?></strong></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <?php foreach ($headers as $h): ?> <th><?= $h ?></th> <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $d): ?>
            <tr>
                <td><?= $d['col_1'] ?></td>
                <td><?= $d['col_2'] ?></td>
                <td><?= $d['col_3'] ?></td>
                <td><?= $d['col_4'] ?></td>
                <td><strong><?= $d['col_5'] ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema NR12 & Manutenção - SENAI</p>
        <p>Este documento é para uso interno e controle de manutenção.</p>
    </div>
</body>
</html>
