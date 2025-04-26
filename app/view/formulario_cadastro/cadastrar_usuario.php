<?php
session_start();
require_once '../../../app/db.php';

$db = new DB();
$pdo = $db->connect();

// Dados do formulário
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

// Criptografar a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO Usuario (nome, usuario, email, senha, cpf, escolaridade, data_nascimento) VALUES (:nome, :usuario, :email, :senha, :cpf, :escolaridade, :data_nascimento)");
    $stmt->execute([
        ':nome' => $nome,
        ':usuario' => $usuario,
        ':email' => $email,
        ':senha' => $senha_hash,
        ':cpf' => $cpf,
        ':escolaridade' => $escolaridade,
        ':data_nascimento' => $data_nascimento
    ]);

    echo "<script>
            alert('✅ Usuário cadastrado com sucesso!');
            window.location.href='../formulario_login/form_login.html';
        </script>";
} catch (PDOException $e) {
    echo "Erro ao cadastrar: " . $e->getMessage();
}
