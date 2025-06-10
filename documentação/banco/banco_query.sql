-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS easym;

USE easym;

-- Tabela de usuários
CREATE TABLE
    Usuario (
        id_usuario INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(100) NOT NULL,
        usuario VARCHAR(20) NOT NULL UNIQUE,
        email VARCHAR(150) NOT NULL UNIQUE,
        senha VARCHAR(255) NOT NULL,
        cpf VARCHAR(14) NOT NULL UNIQUE,
        escolaridade VARCHAR(50) NOT NULL,
        perfil VARCHAR(15),
        data_nascimento DATE NOT NULL
    );

-- Tabela de categorias de despesa personalizadas por usuário
CREATE TABLE
    CategoriaDespesa (
        id_categoria INT AUTO_INCREMENT PRIMARY KEY,
        nome_categoria VARCHAR(100) NOT NULL,
        id_usuario INT NOT NULL,
        UNIQUE (nome_categoria, id_usuario),
        FOREIGN KEY (id_usuario) REFERENCES Usuario (id_usuario) ON DELETE CASCADE
    );

-- Tabela de despesas
CREATE TABLE
    Despesa (
        id_despesa INT AUTO_INCREMENT PRIMARY KEY,
        nome_despesa VARCHAR(100) NOT NULL,
        descricao TEXT,
        valor_despesa DECIMAL(10, 2) NOT NULL,
        data_vencimento DATE NOT NULL,
        id_usuario INT NOT NULL,
        id_categoria INT NOT NULL,
        FOREIGN KEY (id_usuario) REFERENCES Usuario (id_usuario) ON DELETE CASCADE,
        FOREIGN KEY (id_categoria) REFERENCES CategoriaDespesa (id_categoria) ON DELETE CASCADE
    );

-- Tabela de gastos
CREATE TABLE
    Gasto (
        id_gasto INT AUTO_INCREMENT PRIMARY KEY,
        nome_gasto VARCHAR(50) NOT NULL,
        desc_gasto VARCHAR(150),
        categoria_gasto VARCHAR(25) NOT NULL,
        valor_gasto DECIMAL(10, 2) NOT NULL,
        is_imprevisto BOOLEAN NOT NULL DEFAULT FALSE,
        data_gasto DATE NOT NULL,
        id_usuario INT NOT NULL,
        FOREIGN KEY (id_usuario) REFERENCES Usuario (id_usuario) ON DELETE CASCADE
    );

-- Tabela de dívidas (atualizada)
CREATE TABLE
    Divida (
        id_divida INT AUTO_INCREMENT PRIMARY KEY,
        nome_divida VARCHAR(100) NOT NULL,
        taxa_divida DECIMAL(5, 2) NOT NULL,
        categoria_divida VARCHAR(50) NOT NULL,
        valor_total DECIMAL(10, 2) NOT NULL,
        valor_pago DECIMAL(10, 2) NOT NULL,
        data_vencimento DATE NOT NULL,
        id_usuario INT NOT NULL,
        FOREIGN KEY (id_usuario) REFERENCES Usuario (id_usuario) ON DELETE CASCADE
    );

-- Tabela de metas financeiras
CREATE TABLE
    Meta (
        id_meta INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(100) NOT NULL,
        descricao TEXT,
        categoria VARCHAR(50) NOT NULL,
        valor_meta DECIMAL(10, 2) NOT NULL,
        previsao_conclusao DATE NOT NULL,
        id_usuario INT NOT NULL,
        FOREIGN KEY (id_usuario) REFERENCES Usuario (id_usuario) ON DELETE CASCADE
    );

-- Tabela de tetos de gasto
CREATE TABLE
    Teto_gasto (
        id_teto INT AUTO_INCREMENT PRIMARY KEY,
        nome_teto VARCHAR(100) NOT NULL,
        descricao TEXT,
        categoria VARCHAR(50) NOT NULL,
        valor_teto DECIMAL(10, 2) NOT NULL,
        valor_atual DECIMAL(10, 2) DEFAULT 0.00,
        id_usuario INT NOT NULL,
        FOREIGN KEY (id_usuario) REFERENCES Usuario (id_usuario) ON DELETE CASCADE
    );

-- Tabela de entradas de receita
CREATE TABLE
    Entrada (
        id_entrada INT AUTO_INCREMENT PRIMARY KEY,
        descricao VARCHAR(100) NOT NULL,
        valor DECIMAL(10, 2) NOT NULL,
        categoria VARCHAR(50),
        data_entrada DATE NOT NULL,
        id_usuario INT NOT NULL,
        FOREIGN KEY (id_usuario) REFERENCES Usuario (id_usuario) ON DELETE CASCADE
    );

-- Tabela de recuperação de senha
CREATE TABLE
    RecuperacaoSenha (
        id_recuperacao INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL,
        token VARCHAR(64) NOT NULL,
        expiracao DATETIME NOT NULL
    );