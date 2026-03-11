<?php
require 'php/configs/conexao.php';

header('Content-Type: text/plain');

echo "--- INICIANDO REPARO DO BANCO ---\n";

// 1. Corrigir truncamento de status na ordens_servico
$sql1 = "ALTER TABLE ordens_servico MODIFY COLUMN status ENUM('Em Aberto', 'Aguardando Aprovação', 'Aceita', 'Arquivada') NOT NULL DEFAULT 'Em Aberto'";
if ($conn->query($sql1)) {
    echo "[OK] Status ordens_servico corrigido.\n";
} else {
    echo "[ERRO] Status ordens_servico: " . $conn->error . "\n";
}

// 2. Corrigir tamanho do status no histórico
$sql2 = "ALTER TABLE os_historico MODIFY COLUMN status VARCHAR(100) NOT NULL";
if ($conn->query($sql2)) {
    echo "[OK] Status os_historico corrigido.\n";
} else {
    echo "[ERRO] Status os_historico: " . $conn->error . "\n";
}

// 3. Testar se o histórico consegue salvar algo
$sqlTest = "INSERT INTO os_historico (os_id, status, origem_id, destino_id, descricao) VALUES (1, 'Teste Sistema', 1, 1, 'Teste de integridade do histórico')";
if ($conn->query($sqlTest)) {
    echo "[OK] Teste de inserção no histórico funcionou.\n";
    $conn->query("DELETE FROM os_historico WHERE descricao = 'Teste de integridade do histórico'");
} else {
    echo "[ERRO] Falha crítica no teste de histórico: " . $conn->error . "\n";
}

echo "\n--- REPARO FINALIZADO ---\n";
?>
