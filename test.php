<?php
require __DIR__ . '/php/configs/conexao.php';
echo "OS FIELDS:\n";
$res = $conn->query('DESCRIBE ordens_servico');
if($res) {
    while($row = $res->fetch_assoc()){
        echo $row['Field'] . "\n";
    }
}
echo "\nHIST FIELDS:\n";
$res = $conn->query('DESCRIBE os_historico');
if($res) {
    while($row = $res->fetch_assoc()){
        echo $row['Field'] . "\n";
    }
}
