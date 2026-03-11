<?php
// debug_columns.php
require 'php/configs/conexao.php';
header('Content-Type: text/plain');

function print_cols($conn, $table) {
    echo "--- $table ---\n";
    $res = $conn->query("SHOW FULL COLUMNS FROM $table");
    if ($res) {
        while($row = $res->fetch_assoc()) {
            echo "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']} | Default: {$row['Default']} | Extra: {$row['Extra']}\n";
        }
    } else {
        echo "Erro: " . $conn->error . "\n";
    }
    echo "\n";
}

print_cols($conn, 'ordens_servico');
print_cols($conn, 'os_historico');
print_cols($conn, 'os_anexos');
?>
