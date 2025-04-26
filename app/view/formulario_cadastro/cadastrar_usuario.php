<?php
    require_once '../../db.php';

    $nome = $_POST['nome'];
    $usuario_nome = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $cpf = $_POST['cpf'];
    $escolaridade = $_POST['escolaridade'];
    $data_nascimento = $_POST['data_nascimento'];

    if ($senha !== $confirmar_senha) {
        echo "Erro: As senhas não coincidem!";
        exit;
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO Usuario (nome, usuario, email, senha, cpf, escolaridade, data_nascimento)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nome, $usuario_nome, $email, $senha_hash, $cpf, $escolaridade, $data_nascimento);

    if ($stmt->execute()) {
        echo "Cadastro realizado com sucesso!";
    } else {
        echo "Erro ao cadastrar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
?>
