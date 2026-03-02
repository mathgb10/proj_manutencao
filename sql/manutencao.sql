-- Tabela para armazenar os itens de verificação de cada máquina
-- Já utilizada em register_machines.php
CREATE TABLE IF NOT EXISTS checklist_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT NOT NULL,
    item_verificacao VARCHAR(255) NOT NULL,
    frequencia VARCHAR(50) NOT NULL, -- mensal, trimestral, semestral, anual, bianual
    FOREIGN KEY (maquina_id) REFERENCES maquinas(id) ON DELETE CASCADE
);

-- Tabela para histórico de manutenções realizadas
CREATE TABLE IF NOT EXISTS historico_manutencao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT NOT NULL,
    usuario_id INT, -- Quem realizou a manutenção (opcional, se tiver sistema de login)
    data_realizada DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacoes TEXT,
    FOREIGN KEY (maquina_id) REFERENCES maquinas(id) ON DELETE CASCADE
);

-- Tabela para detalhar quais itens foram verificados em uma manutenção específica
-- Isso permite saber se todos os itens foram checados ou apenas alguns
CREATE TABLE IF NOT EXISTS historico_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    historico_id INT NOT NULL,
    item_checklist_id INT NOT NULL,
    status ENUM('conforme', 'nao_conforme', 'nao_verificado') DEFAULT 'conforme',
    observacao_item TEXT,
    FOREIGN KEY (historico_id) REFERENCES historico_manutencao(id) ON DELETE CASCADE,
    FOREIGN KEY (item_checklist_id) REFERENCES checklist_itens(id) ON DELETE CASCADE
);
