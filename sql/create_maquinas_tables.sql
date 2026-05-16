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
