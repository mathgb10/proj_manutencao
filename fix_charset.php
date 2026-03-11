<?php
// fix_charset.php
require 'php/configs/conexao.php';
header('Content-Type: text/plain');

echo "--- CORRIGINDO CHARSET E ENUM ---\n";

// 1. Garantir que a conexão use UTF-8
$conn->set_charset("utf8mb4");

// 2. Converter Tabelas
$tables = ['ordens_servico', 'os_historico', 'os_anexos'];
foreach ($tables as $table) {
    $sql = "ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "[OK] Tabela $table convertida para utf8mb4.\n";
    } else {
        echo "[ERRO] Falha ao converter $table: " . $conn->error . "\n";
    }
}

// 3. Corrigir o ENUM corrompido
$sqlEnum = "ALTER TABLE ordens_servico MODIFY COLUMN tipo ENUM('Manutenção', 'Patrimônio', 'Outros') NOT NULL";
if ($conn->query($sqlEnum)) {
    echo "[OK] ENUM 'tipo' corrigido com acentuação correta.\n";
} else {
    echo "[ERRO] Falha ao corrigir ENUM 'tipo': " . $conn->error . "\n";
}

// 4. Corrigir Status também por precaução
$sqlStatus = "ALTER TABLE ordens_servico MODIFY COLUMN status ENUM('Em Aberto', 'Aguardando Aprovação', 'Aceita', 'Arquivada') NOT NULL DEFAULT 'Em Aberto'";
if ($conn->query($sqlStatus)) {
    echo "[OK] ENUM 'status' reafirmado.\n";
} else {
    echo "[ERRO] Falha ao reafirmar ENUM 'status': " . $conn->error . "\n";
}

echo "\n--- PROCESSO CONCLUÍDO ---\n";
?>
