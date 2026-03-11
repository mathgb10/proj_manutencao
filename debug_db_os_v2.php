<?php
require 'php/configs/conexao.php';

header('Content-Type: text/plain');

echo "--- LISTA TABELAS ---\n";
$res = $conn->query("SHOW TABLES");
while($row = $res->fetch_array()) {
    echo $row[0] . "\n";
}

echo "\n--- COLUNAS ordens_servico ---\n";
$res = $conn->query("SHOW COLUMNS FROM ordens_servico");
while($row = $res->fetch_assoc()) {
    echo "Field: {$row['Field']} - Type: {$row['Type']} - Null: {$row['Null']}\n";
}

echo "\n--- COLUNAS os_historico ---\n";
$res = $conn->query("SHOW COLUMNS FROM os_historico");
while($row = $res->fetch_assoc()) {
    echo "Field: {$row['Field']} - Type: {$row['Type']} - Null: {$row['Null']}\n";
}

echo "\n--- DADOS RECENTES (ÚLTIMAS 5) ---\n";
$res = $conn->query("SELECT * FROM ordens_servico ORDER BY id DESC LIMIT 5");
while($row = $res->fetch_assoc()) {
    echo "ID: {$row['id']} | STATUS: [{$row['status']}] | DESC: " . substr($row['descricao'], 0, 30) . "...\n";
}

echo "\n--- HISTÓRICO RECENTE (ÚLTIMOS 5) ---\n";
$res = $conn->query("SELECT * FROM os_historico ORDER BY id DESC LIMIT 5");
if ($res) {
    while($row = $res->fetch_assoc()) {
        echo "ID: {$row['id']} | OS_ID: {$row['os_id']} | STATUS: [{$row['status']}] | DESC: " . substr($row['descricao'], 0, 30) . "...\n";
    }
} else {
    echo "Erro ao ler histórico: " . $conn->error;
}
?>
