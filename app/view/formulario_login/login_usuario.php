<?php
session_start();
require_once '../../../app/db.php';

$db = new DB();
$pdo = $db->connect();

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

$stmt = $pdo->prepare("SELECT id, nome, senha FROM Usuario WHERE email = :usuario OR usuario = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificando senha criptografada
    if (password_verify($senha, $row['senha'])) {
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['usuario_nome'] = $row['nome'];

        header("Location: ../../view/dashboard.php");
        exit;
    } else {
        echo "<script>alert('Senha incorreta!'); window.location.href='form_login.html';</script>";
    }
} else {
    echo "<script>alert('Usuário não encontrado!'); window.location.href='form_login.html';</script>";
}
?>
