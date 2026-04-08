-- Script para criar a tabela tipomaquina no banco manutencao_tds2026
USE manutencao_tds2026;

CREATE TABLE IF NOT EXISTS tipomaquina (
  idtipomaquina int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  tipomaquina_nome varchar(80) NOT NULL,
  tipomaquina_status enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo',
  tipomaquina_arquivo varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Opcional: Migração inicial de dados do NR12 para Manutenção
-- INSERT INTO manutencao_tds2026.tipomaquina (idtipomaquina, tipomaquina_nome, tipomaquina_status, tipomaquina_arquivo)
-- SELECT idtipomaquina, tipomaquina_nome, tipomaquina_status, tipomaquina_arquivo FROM nr12.tipomaquina;
