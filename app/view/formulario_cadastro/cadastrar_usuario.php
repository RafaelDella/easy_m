<?php
    session_start();
    require_once '../../../app/db.php';

    $db = new DB();
    $conn = $db->connect(); // ⚡ agora $conn existe!

    // Dados recebidos
    $nome = $_POST['nome'];
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $cpf = $_POST['cpf'];
    $escolaridade = $_POST['escolaridade'];
    $data_nascimento = $_POST['data_nascimento'];

    if ($senha !== $confirmar_senha) {
        die("As senhas não coincidem.");
    }

    // Agora usando PDO:
    $stmt = $conn->prepare("INSERT INTO Usuario (nome, usuario, email, senha, cpf, escolaridade, data_nascimento) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nome, $usuario, $email, $senha, $cpf, $escolaridade, $data_nascimento]);

    echo "Usuário cadastrado com sucesso!";
    header("Location: ../formulario_login/form_login.html "); // Redireciona para a página de login
    exit;
?>

