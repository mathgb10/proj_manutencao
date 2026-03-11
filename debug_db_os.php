<?php
require 'php/configs/conexao.php';

header('Content-Type: text/plain');

echo "--- ESTRUTURA ordens_servico ---\n";
$res = $conn->query("DESCRIBE ordens_servico");
while($row = $res->fetch_assoc()) {
    print_r($row);
}

echo "\n--- ESTRUTURA os_historico ---\n";
$res = $conn->query("DESCRIBE os_historico");
while($row = $res->fetch_assoc()) {
    print_r($row);
}

echo "\n--- ÚLTIMAS 5 ORDENS ---\n";
$res = $conn->query("SELECT * FROM ordens_servico ORDER BY id DESC LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
