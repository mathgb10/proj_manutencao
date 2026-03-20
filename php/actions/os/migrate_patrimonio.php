<?php
require '../../configs/conexao.php';

header('Content-Type: text/plain');

echo "--- INICIANDO MIGRAÇÃO: ADICIONAR COLUNA PATRIMONIO ---\n";

// 1. Adicionar a coluna patrimonio à tabela ordens_servico
$sql = "ALTER TABLE ordens_servico ADD COLUMN patrimonio VARCHAR(255) NULL AFTER tipo";

if ($conn->query($sql)) {
    echo "[OK] Coluna 'patrimonio' adicionada com sucesso.\n";
} else {
    if (strpos($conn->error, "Duplicate column name") !== false) {
        echo "[INFO] A coluna 'patrimonio' já existe.\n";
    } else {
        echo "[ERRO] Erro ao adicionar coluna: " . $conn->error . "\n";
    }
}

echo "\n--- MIGRAÇÃO FINALIZADA ---\n";
?>
