<?php
require '../../configs/conexao.php';

header('Content-Type: text/plain');

echo "--- CRIANDO TABELAS DE ORDEM DE SERVIÇO ---\n";

$sqls = [
    "CREATE TABLE IF NOT EXISTS ordens_servico (
        id INT AUTO_INCREMENT PRIMARY KEY,
        descricao TEXT NOT NULL,
        tipo ENUM('Corretivo','Outros') NOT NULL,
        patrimonio VARCHAR(255) NULL,
        status ENUM('Em Aberto','Aguardando Aprovação','Aceita','Arquivada') NOT NULL DEFAULT 'Em Aberto',
        solicitante_id INT NOT NULL,
        responsavel_id INT NOT NULL,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
        atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (solicitante_id) REFERENCES usuarios(id),
        FOREIGN KEY (responsavel_id) REFERENCES usuarios(id)
    )",
    "CREATE TABLE IF NOT EXISTS os_historico (
        id INT AUTO_INCREMENT PRIMARY KEY,
        os_id INT NOT NULL,
        status VARCHAR(100) NOT NULL,
        origem_id INT NOT NULL,
        destino_id INT NOT NULL,
        descricao TEXT,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE,
        FOREIGN KEY (origem_id) REFERENCES usuarios(id),
        FOREIGN KEY (destino_id) REFERENCES usuarios(id)
    )",
    "CREATE TABLE IF NOT EXISTS os_anexos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        os_id INT NOT NULL,
        nome_arquivo VARCHAR(255) NOT NULL,
        caminho VARCHAR(500) NOT NULL,
        criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE
    )"
];

foreach ($sqls as $index => $sql) {
    if ($conn->query($sql)) {
        echo "[OK] Tabela " . ($index + 1) . " criada/verificada.\n";
    } else {
        echo "[ERRO] Tabela " . ($index + 1) . ": " . $conn->error . "\n";
    }
}

echo "\n--- PROCESSO FINALIZADO ---\n";
?>
