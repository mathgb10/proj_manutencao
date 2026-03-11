<?php
// fix_db_os_v2.php
require 'php/configs/conexao.php';

header('Content-Type: text/plain');

echo "--- INICIANDO REPARO SEGURO DO BANCO ---\n";

// 1. Corrigir truncamento de status na ordens_servico
// Primeiro, vamos ver o tipo atual
$res = $conn->query("SHOW COLUMNS FROM ordens_servico LIKE 'status'");
$col = $res->fetch_assoc();
echo "Status Atual ordens_servico: " . $col['Type'] . "\n";

$sql1 = "ALTER TABLE ordens_servico MODIFY COLUMN status ENUM('Em Aberto', 'Aguardando Aprovação', 'Aceita', 'Arquivada') NOT NULL DEFAULT 'Em Aberto'";
if ($conn->query($sql1)) {
    echo "[OK] Status ordens_servico atualizado/corrigido.\n";
} else {
    echo "[ERRO] Status ordens_servico: " . $conn->error . "\n";
}

// 2. Corrigir tamanho do status no histórico
$resH = $conn->query("SHOW COLUMNS FROM os_historico LIKE 'status'");
if ($resH && $colH = $resH->fetch_assoc()) {
    echo "Status Atual os_historico: " . $colH['Type'] . "\n";
    $sql2 = "ALTER TABLE os_historico MODIFY COLUMN status VARCHAR(100) NOT NULL";
    if ($conn->query($sql2)) {
        echo "[OK] Status os_historico corrigido.\n";
    } else {
        echo "[ERRO] Status os_historico: " . $conn->error . "\n";
    }
} else {
    echo "[AVISO] Tabela os_historico ou coluna status não encontrada.\n";
}

echo "\n--- REPARO FINALIZADO ---\n";
?>
