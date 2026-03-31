<?php
require __DIR__ . '/../../configs/conexao.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID da máquina não fornecido']);
    exit;
}

$maquina_id = intval($_GET['id']);

// Busca itens da checklist
$sql = "SELECT * FROM checklist_itens WHERE maquina_id = $maquina_id";
$result = $conn->query($sql);

$itens = [];
while ($row = $result->fetch_assoc()) {
    // Para cada item, buscar a última vez que foi verificado
    // Usando a tabela historico_itens ligada a historico_manutencao
    $itemId = $row['id'];

    $sqlHist = "SELECT h.data_realizada 
                FROM historico_itens hi
                JOIN historico_manutencao h ON hi.historico_id = h.id
                WHERE hi.item_checklist_id = $itemId
                ORDER BY h.data_realizada DESC LIMIT 1";

    $resHist = $conn->query($sqlHist);
    $ultimaData = null;
    if ($resHist && $r = $resHist->fetch_assoc()) {
        $ultimaData = $r['data_realizada'];
    }

    $row['ultima_data'] = $ultimaData ? date('d/m/Y', strtotime($ultimaData)) : 'Nunca';

    // Calculando próxima data (Simplificado, ideal seria usar biblioteca de data)
    // Frequencia: mensal, trimestral, semestral, anual
    $proxima = null;
    if ($ultimaData) {
        $intervalo = '+1 month';
        if (stripos($row['frequencia'], 'trimestral') !== false)
            $intervalo = '+3 months';
        if (stripos($row['frequencia'], 'semestral') !== false)
            $intervalo = '+6 months';
        if (stripos($row['frequencia'], 'anual') !== false)
            $intervalo = '+1 year';

        $proxima = date('Y-m-d', strtotime($ultimaData . " $intervalo"));
        $row['proxima_data_iso'] = $proxima;
        $row['proxima_data'] = date('d/m/Y', strtotime($proxima));
    } else {
        $row['proxima_data'] = 'Pendente';
        $row['proxima_data_iso'] = date('Y-m-d'); // Se nunca fez, é pra hoje!
    }

    $itens[] = $row;
}

echo json_encode($itens);
?>
