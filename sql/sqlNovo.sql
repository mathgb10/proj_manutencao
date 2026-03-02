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

CREATE TABLE IF NOT EXISTS manutencao_corretiva (
    id INT AUTO_INCREMENT PRIMARY KEY,
    maquina_id INT NOT NULL,
    data_ocorrencia DATE NOT NULL,
    responsavel VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maquina_id) REFERENCES maquinas(id) ON DELETE CASCADE
);