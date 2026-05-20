<?php
// Just a quick script to test the DB
require __DIR__ . '/php/configs/conexao.php';
$sql = "SELECT id, status, tipo FROM ordens_servico";
$res = $conn->query($sql);
$counts = ['Em Aberto' => 0, 'Aceita' => 0, 'Aguardando Aprovação' => 0, 'Arquivada' => 0];
while($row = $res->fetch_assoc()){
    if(isset($counts[$row['status']])) $counts[$row['status']]++;
}
print_r($counts);
