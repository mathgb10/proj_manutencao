<?php
// fix_fk_historico.php
require 'php/configs/conexao.php';
header('Content-Type: text/plain');

echo "--- CORRIGINDO CHAVE ESTRANGEIRA DO HISTÓRICO ---\n";

// 1. Identificar o nome exato da constraint problemática
$res = $conn->query("SHOW CREATE TABLE os_historico");
$row = $res->fetch_assoc();
echo "Estrutura Atual:\n" . $row['Create Table'] . "\n\n";

// 2. Tentar remover a constraint errada (geralmente os_historico_ibfk_1)
$sqlDrop = "ALTER TABLE os_historico DROP FOREIGN KEY os_historico_ibfk_1";
if ($conn->query($sqlDrop)) {
    echo "[OK] Constraint antiga removida com sucesso.\n";
} else {
    echo "[AVISO/ERRO] Não foi possível remover a constraint: " . $conn->error . "\n";
}

// 3. Adicionar a constraint correta apontando para ordens_servico (tabela oficial)
$sqlAdd = "ALTER TABLE os_historico ADD CONSTRAINT fk_os_id_principal 
           FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE";

if ($conn->query($sqlAdd)) {
    echo "[OK] Chave Estrangeira corrigida: Agora aponta para a tabela oficial 'ordens_servico'.\n";
} else {
    echo "[ERRO] Falha ao adicionar a nova FK: " . $conn->error . "\n";
}

echo "\n--- PROCESSO CONCLUÍDO ---\n";
?>
