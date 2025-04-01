CREATE DATABASE easym;
USE easym;

CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,  -- Precisa Criptografar
    cpf VARCHAR(14) NOT NULL UNIQUE,
    escolaridade VARCHAR(50) NULL,
    perfil VARCHAR(15) NULL, -- Perfil de usuário
    data_nascimento DATE NOT NULL,
    idade INT GENERATED ALWAYS AS (TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE())) STORED -- Calculando idade
);

CREATE TABLE Gasto (
    id_gasto INT AUTO_INCREMENT PRIMARY KEY,
    nome_gasto VARCHAR(50) NOT NULL,
    desc_gasto VARCHAR(150) NULL,
    categoria_gasto VARCHAR(25) NOT NULL,
    valor_gasto DECIMAL(10,2) NOT NULL,
    is_imprevisto BOOLEAN NOT NULL DEFAULT FALSE,
    data_gasto DATE NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE Divida (
    id_divida INT AUTO_INCREMENT PRIMARY KEY,
    nome_divida VARCHAR(100) NOT NULL,
    taxa_divida DECIMAL(5,2) NOT NULL,
    categoria_divida VARCHAR(50) NOT NULL,
    data_divida DATE NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE Meta (
    id_meta INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    categoria VARCHAR(50) NOT NULL,
    valor_meta DECIMAL(10,2) NOT NULL,
    previsao_conclusao DATE NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE teto_gasto (
    id_teto INT AUTO_INCREMENT PRIMARY KEY,
    nome_teto VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    categoria VARCHAR(50) NOT NULL, -- Qual área se aplica
    valor_teto DECIMAL(10,2) NOT NULL, -- Valor limite para os gastos
    valor_atual DECIMAL(10,2) DEFAULT 0.00, -- Soma dos gastos
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE categoria_personalizada (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nome_categoria VARCHAR(50) NOT NULL UNIQUE,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id) ON DELETE CASCADE
);