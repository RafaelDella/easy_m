<?php
    session_start();
    require_once '../../../app/db.php';

    // Conexão usando a classe DB
    $db = new DB();
    $conn = $db->connect();

    // Dados do formulário
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Consulta segura com PDO
    $stmt = $conn->prepare("SELECT id, nome, senha FROM Usuario WHERE email = :usuario OR usuario = :usuario");
    $stmt->bindParam(':usuario', $usuario);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificação da senha (atualmente sem password_hash, comparação direta)
        if ($senha === $row['senha']) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nome'] = $row['nome'];

            header("Location: ../dashboard.php"); // Redireciona para a página principal
            exit;
        } else {
            echo "<script>alert('Senha incorreta'); window.location.href='form_login.html';</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado'); window.location.href='form_login.html';</script>";
    }
?>
