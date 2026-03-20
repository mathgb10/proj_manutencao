-- =============================================
-- MIGRATION FINAL: Sistema de O.S.
-- Executar no banco: manutencao_tds2026
-- Execute cada bloco separadamente.
-- Erros de "Duplicate column" podem ser ignorados.
-- =============================================
USE manutencao_tds2026;

-- =============================================
-- PASSO 1: Corrigir FK do histórico (aponta para tabela backup errada)
-- =============================================
ALTER TABLE os_historico DROP FOREIGN KEY os_historico_ibfk_1;
ALTER TABLE os_historico ADD CONSTRAINT os_historico_ibfk_1
    FOREIGN KEY (os_id) REFERENCES ordens_servico(id) ON DELETE CASCADE;

-- =============================================
-- PASSO 2: Adicionar colunas que faltam na ordens_servico
-- (execute um por vez, ignore se já existir)
-- =============================================
ALTER TABLE ordens_servico ADD COLUMN patrimonio VARCHAR(255) NULL AFTER tipo;
ALTER TABLE ordens_servico ADD COLUMN gasto DECIMAL(10,2) NULL AFTER anterior_responsavel_id;
ALTER TABLE ordens_servico ADD COLUMN obs_finalizacao TEXT NULL AFTER gasto;

-- =============================================
-- PASSO 3: Ajustar ENUMs
-- =============================================
UPDATE ordens_servico SET tipo = 'Outros' WHERE tipo NOT IN ('Corretivo');
ALTER TABLE ordens_servico MODIFY COLUMN tipo ENUM('Corretivo','Outros') NOT NULL DEFAULT 'Corretivo';
ALTER TABLE ordens_servico MODIFY COLUMN status ENUM('Em Aberto','Aguardando Aprovação','Aceita','Arquivada','Recusada') NOT NULL DEFAULT 'Em Aberto';
