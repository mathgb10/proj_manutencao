<?php
// debug_columns_file.php
require 'php/configs/conexao.php';
$output = "";

function get_cols($conn, $table) {
    $out = "--- $table ---\n";
    $res = $conn->query("SHOW FULL COLUMNS FROM $table");
    if ($res) {
        while($row = $res->fetch_assoc()) {
            $out .= "Field: {$row['Field']} | Type: {$row['Type']} | Null: {$row['Null']} | Default: " . ($row['Default'] ?? 'NULL') . " | Extra: {$row['Extra']}\n";
        }
    } else {
        $out .= "Erro: " . $conn->error . "\n";
    }
    $out .= "\n";
    return $out;
}

$output .= get_cols($conn, 'ordens_servico');
$output .= get_cols($conn, 'os_historico');
$output .= get_cols($conn, 'os_anexos');

file_put_contents('c:/xampp/htdocs/nr12/manutencaoMath/db_structure_fixed.txt', $output);
echo "Dados salvos em db_structure_fixed.txt";
?>
