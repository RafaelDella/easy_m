<?php
session_start();
require_once '../../db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../formulario_login/form_login.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Captura os dados enviados via POST
$nome = $_POST['nome'];
$email = $_POST['email'];
$usuario = $_POST['usuario'];
$cpf = $_POST['cpf'];
$escolaridade = $_POST['escolaridade'];
$data_nascimento = $_POST['data_nascimento'];

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("UPDATE Usuario SET 
        nome = :nome, 
        email = :email, 
        usuario = :usuario, 
        cpf = :cpf, 
        escolaridade = :escolaridade, 
        data_nascimento = :data_nascimento 
        WHERE id = :id");

    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'usuario' => $usuario,
        'cpf' => $cpf,
        'escolaridade' => $escolaridade,
        'data_nascimento' => $data_nascimento,
        'id' => $usuario_id
    ]);

    echo "<script>
        alert('✅ Perfil atualizado com sucesso!');
        window.location.href = '../dashboard.php';
    </script>";
} catch (PDOException $e) {
    echo "Erro ao atualizar perfil: " . $e->getMessage();
}
