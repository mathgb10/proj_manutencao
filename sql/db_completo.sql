-- =============================================
-- DATABASE: manutencao_tds2026
-- CONSOLIDATED Master Script
-- Created: 2026-03-23
-- =============================================

CREATE DATABASE IF NOT EXISTS manutencao_tds2026;
USE manutencao_tds2026;

-- =============================================
-- USERS AND BASE DATA
-- =============================================

CREATE TABLE IF NOT EXISTS usuarios(
	id INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    senha VARCHAR(245) NOT NULL,
    permissao ENUM('ADMIN','GESTOR','NORMAL') NOT NULL DEFAULT 'NORMAL'
);

INSERT IGNORE INTO `usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES 
('Mathues', 'matheus@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN'),
('Miguel', 'miguel@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN'),
('Ruan Duas Torres', 'ruan@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN'),
('Pereira', 'pereira@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN'),
('Lais', 'lais@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN'),
('Gideao', 'gideao@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN'),
('Pedro', 'pedro@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN'),
('Kaua Reis', 'kaua@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');

-- =============================================
-- MACHINES AND CHECKLISTS
-- =============================================

CREATE TABLE IF NOT EXISTS maquinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    denominacao VARCHAR(255) NOT NULL,
    marca VARCHAR(255),
    modelo VARCHAR(255),
    numero_identificacao VARCHAR(100),
    tipomaquina_id INT NULL,
    ano_fabricacao INT,
    setor VARCHAR(100),
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_proxima_manutencao DATE NULL
);

CREATE TABLE IF NOT EXISTS checklist_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT NOT NULL,
    item_verificacao VARCHAR(255) NOT NULL,
    frequencia VARCHAR(100) NOT NULL,
    FOREIGN KEY (maquina_id) REFERENCES maquinas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS acessorios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT NOT NULL,
    denominacao VARCHAR(255) NOT NULL,
    aplicacao VARCHAR(255),
    caracteristicas VARCHAR(255),
    numero_identificacao VARCHAR(100),
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maquina_id) REFERENCES maquinas(id) ON DELETE CASCADE
);

-- =============================================
-- MAINTENANCE HISTORY
-- =============================================

CREATE TABLE IF NOT EXISTS historico_manutencao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT NOT NULL,
    usuario_id INT,
    data_realizada DATETIME DEFAULT CURRENT_TIMESTAMP,
    observacoes TEXT,
    FOREIGN KEY (maquina_id) REFERENCES maquinas(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS historico_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    historico_id INT NOT NULL,
    item_checklist_id INT NOT NULL,
    status ENUM('conforme', 'nao_conforme', 'nao_verificado') DEFAULT 'conforme',
    observacao_item TEXT,
    FOREIGN KEY (historico_id) REFERENCES historico_manutencao(id) ON DELETE CASCADE,
    FOREIGN KEY (item_checklist_id) REFERENCES checklist_itens(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS manutencao_corretiva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT NOT NULL,
    data_ocorrencia DATE NOT NULL,
    responsavel VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maquina_id) REFERENCES maquinas(id) ON DELETE CASCADE
);

-- =============================================
-- SERVICE ORDERS (O.S.)
-- =============================================

CREATE TABLE IF NOT EXISTS ordens_servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao TEXT NOT NULL,
    tipo ENUM('Manutenção','Patrimônio','Outros','Corretivo') NOT NULL DEFAULT 'Manutenção',
    patrimonio VARCHAR(255) NULL,
    status ENUM('Em Aberto','Aguardando Aprovação','Aceita','Arquivada','Recusada') NOT NULL DEFAULT 'Em Aberto',
    solicitante_id INT NOT NULL,
    responsavel_id INT NOT NULL,
    anterior_responsavel_id INT NULL,
    gasto DECIMAL(10,2) NULL,
    obs_finalizacao TEXT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id),
    FOREIGN KEY (responsavel_id) REFERENCES usuarios(id)
);

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

CREATE TABLE IF NOT EXISTS os_anexos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    os_id INT NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho VARCHAR(500) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE
);

-- =============================================
-- SYSTEM LOGS
-- =============================================

CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    ip_address VARCHAR(45) NOT NULL,
    sql_command TEXT NOT NULL,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- FINAL ADJUSTMENTS AND MIGRATIONS
-- =============================================

-- Atualizando tipos de O.S. para padrão Corretivo se for o caso
UPDATE ordens_servico SET tipo = 'Corretivo' WHERE tipo IN ('Manutenção');

-- Garantindo que o ENUM Final seja respeitado (Corretivo, Outros)
ALTER TABLE ordens_servico MODIFY COLUMN tipo ENUM('Corretivo','Outros') NOT NULL DEFAULT 'Corretivo';
