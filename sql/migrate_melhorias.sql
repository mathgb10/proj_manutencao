-- =============================================
-- MIGRATION: Melhorias no Sistema de OS
-- Executar no banco: manutencao_tds2026
-- =============================================
USE manutencao_tds2026;

-- 1. Adicionar campo para guardar o responsável anterior (para recusar = volta ao anterior)
ALTER TABLE ordens_servico
    ADD COLUMN IF NOT EXISTS anterior_responsavel_id INT NULL AFTER responsavel_id,
    ADD COLUMN IF NOT EXISTS gasto DECIMAL(10,2) NULL AFTER anterior_responsavel_id,
    ADD COLUMN IF NOT EXISTS obs_finalizacao TEXT NULL AFTER gasto;

-- 2. Ajustar ENUM tipo: manter apenas Manutencao e Corretivo
-- ATENÇÃO: converter os registros existentes antes de alterar
UPDATE ordens_servico SET tipo = 'Manutenção' WHERE tipo NOT IN ('Manutenção', 'Corretivo');
ALTER TABLE ordens_servico MODIFY COLUMN tipo ENUM('Manutenção','Corretivo') NOT NULL DEFAULT 'Corretivo';

-- 3. Ajustar ENUM status: adicionar 'Recusada'
ALTER TABLE ordens_servico MODIFY COLUMN status ENUM('Em Aberto','Aguardando Aprovação','Aceita','Arquivada','Recusada') NOT NULL DEFAULT 'Em Aberto';

-- 4. FK para anterior_responsavel_id
ALTER TABLE ordens_servico
    ADD CONSTRAINT fk_anterior_responsavel
    FOREIGN KEY (anterior_responsavel_id) REFERENCES usuarios(id) ON DELETE SET NULL;
