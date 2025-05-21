CREATE DATABASE easym;
USE easym;

CREATE TABLE Usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,  -- Lembrar de aplicar hash na aplicação
    cpf VARCHAR(14) NOT NULL UNIQUE,
    escolaridade VARCHAR(50) NOT NULL,
    perfil VARCHAR(15), -- Perfil de usuário (Endividado, Poupador, Doméstico)
    data_nascimento DATE NOT NULL
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
    valor DECIMAL(10,2) NOT NULL,
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
    previsao_conclusao DATE NOT NULL, -- Calculadora de Juros com Amortização ou Definir pelo usuário as parcelas restantes
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE Teto_gasto (
    id_teto INT AUTO_INCREMENT PRIMARY KEY,
    nome_teto VARCHAR(100) NOT NULL,
    descricao TEXT NULL,
    categoria VARCHAR(50) NOT NULL, -- Qual área se aplica
    valor_teto DECIMAL(10,2) NOT NULL, -- Valor limite para os gastos
    valor_atual DECIMAL(10,2) DEFAULT 0.00, -- Soma dos gastos
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE Entrada (
    id_entrada INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(100) NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50), -- Selector no front
    data_entrada DATE NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE Despesa (
    id_despesa INT AUTO_INCREMENT PRIMARY KEY,
    nome_despesa VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50) NOT NULL,
    valor_despesa DECIMAL(10,2) NOT NULL, 
    data_vencimento DATE NOT NULL,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE Despesa (
    id_despesa INT AUTO_INCREMENT PRIMARY KEY,
    nome_despesa VARCHAR(100) NOT NULL,
    descricao TEXT,
    categoria VARCHAR(50) NOT NULL,
    valor_despesa DECIMAL(10,2) NOT NULL, 
    data_vencimento DATE NOT NULL,
    id_usuario INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id) ON DELETE CASCADE
);

CREATE TABLE RecuperacaoSenha (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expiracao DATETIME NOT NULL
);