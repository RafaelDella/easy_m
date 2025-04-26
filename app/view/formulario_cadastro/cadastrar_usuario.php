<?php
    // 1. Configurações do banco
    $host = 'localhost';
    $usuario = 'root';       // Altere se for diferente
    $senha = '';             // Altere se houver senha
    $banco = 'easym';        // Substitua pelo nome real do seu banco

    // 2. Conectar ao banco
    $conn = new mysqli($host, $usuario, $senha, $banco);

    // 3. Verificar conexão
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // 4. Coletar dados do formulário
    $nome = $_POST['nome'];
    $usuario_nome = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $cpf = $_POST['cpf'];
    $escolaridade = $_POST['escolaridade'];
    $data_nascimento = $_POST['data_nascimento'];

    // 5. Verificar se senhas coincidem
    if ($senha !== $confirmar_senha) {
        echo "Erro: As senhas não coincidem!";
        exit;
    }

    // 6. Criptografar a senha (seguro)
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // 7. Preparar e executar o INSERT
    $sql = "INSERT INTO Usuario (nome, usuario, email, senha, cpf, escolaridade, data_nascimento)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nome, $usuario_nome, $email, $senha_hash, $cpf, $escolaridade, $data_nascimento);

    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    // 8. Fechar conexão
    $stmt->close();
    $conn->close();
?>
