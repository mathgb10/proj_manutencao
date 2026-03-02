CREATE DATABASE IF NOT EXISTS manutencao_tds2026;
USE manutencao_tds2026;

CREATE TABLE IF NOT EXISTS usuarios(
	id INT PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(200) NOT NULL,
    email VARCHAR(200) NOT NULL,
    senha VARCHAR(245) NOT NULL,
    permissao ENUM('ADMIN','GESTOR','NORMAL') NOT NULL DEFAULT 'NORMAL'
);

INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Mathues', 'matheus@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Miguel', 'miguel@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Ruan Duas Torres', 'ruan@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Pereira', 'pereira@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Lais', 'lais@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Gideao', 'gideao@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Pedro', 'pedro@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
INSERT INTO `manutencao_tds2026`.`usuarios` (`nome`, `email`, `senha`, `permissao`) VALUES ('Kaua Reis', 'kaua@email.com', '$2b$12$ZEdrFyLzc.prScThSUw9leyeRLeNUkQsuJxOC1BpjusJ74au5dg9m', 'ADMIN');
