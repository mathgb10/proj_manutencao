-- Migra a tabela canonica de maquinas para sustentar os fluxos do NR12.
-- Execute depois de consolidar manutencao_tds2026.maquinas como fonte unica.

USE manutencao_tds2026;

ALTER TABLE maquinas
  ADD COLUMN IF NOT EXISTS tipomaquina_id INT NULL AFTER numero_identificacao,
  ADD COLUMN IF NOT EXISTS data_proxima_manutencao DATE NULL AFTER criado_em;

UPDATE manutencao_tds2026.maquinas AS mm
INNER JOIN nr12.maquina AS nm
  ON nm.maquina_ni = mm.numero_identificacao
SET
  mm.tipomaquina_id = COALESCE(mm.tipomaquina_id, nm.tipomaquina_id),
  mm.data_proxima_manutencao = COALESCE(mm.data_proxima_manutencao, nm.data_proxima_manutencao);
