<?php
require __DIR__ . '/php/configs/conexao.php';
$r = $conn->query("SELECT id, denominacao, numero_identificacao, setor FROM maquinas");
while($row = $r->fetch_assoc()) {
    print_r($row);
}
