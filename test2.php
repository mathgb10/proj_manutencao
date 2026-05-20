<?php
require __DIR__ . '/php/configs/conexao.php';
$sql = "SELECT
            os.id,
            os.status
        FROM ordens_servico os
        WHERE 1=1";
$res = $conn->query($sql);
echo "TOTAL OS: " . $res->num_rows . "\n";
while($row = $res->fetch_assoc()){
    echo "ID: " . $row['id'] . " - Status: " . $row['status'] . "\n";
}
