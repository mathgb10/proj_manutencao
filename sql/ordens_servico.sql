USE manutencao_tds2026;

-- =============================================
-- Tabela principal de Ordens de Serviço
-- =============================================
CREATE TABLE IF NOT EXISTS ordens_servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao TEXT NOT NULL,
    tipo ENUM('Manutenção','Patrimônio','Outros') NOT NULL,
    patrimonio VARCHAR(255) NULL,
    status ENUM('Em Aberto','Aguardando Aprovação','Aceita','Arquivada') NOT NULL DEFAULT 'Em Aberto',
    solicitante_id INT NOT NULL,
    responsavel_id INT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id),
    FOREIGN KEY (responsavel_id) REFERENCES usuarios(id)
);

-- =============================================
-- Histórico de movimentações da O.S.
-- =============================================
CREATE TABLE IF NOT EXISTS os_historico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    os_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    origem_id INT NOT NULL,
    destino_id INT NOT NULL,
    descricao TEXT,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE,
    FOREIGN KEY (origem_id) REFERENCES usuarios(id),
    FOREIGN KEY (destino_id) REFERENCES usuarios(id)
);

-- =============================================
-- Anexos da O.S.
-- =============================================
CREATE TABLE IF NOT EXISTS os_anexos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    os_id INT NOT NULL,
    historico_id INT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho VARCHAR(500) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE,
    FOREIGN KEY (historico_id) REFERENCES os_historico(id) ON DELETE SET NULL
);

