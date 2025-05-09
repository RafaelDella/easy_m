<?php
session_start();
require_once '../../db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../formulario_login/form_login.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$db = new DB();
$pdo = $db->connect();

try {
    // Remove o usuário
    $stmt = $pdo->prepare("DELETE FROM Usuario WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);

    // Destroi a sessão
    session_destroy();

    echo "<script>
        alert('✅ Conta excluída com sucesso!');
        window.location.href = '../formulario_login/form_login.html';
    </script>";
} catch (PDOException $e) {
    echo "Erro ao excluir conta: " . $e->getMessage();
}
