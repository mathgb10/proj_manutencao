<?php
require 'php/configs/conexao.php';

header('Content-Type: text/plain');

$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        echo $row[0] . "\n";
    }
} else {
    echo "Erro ao listar tabelas: " . $conn->error;
}
?>
