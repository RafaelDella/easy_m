<?php
session_start();
require_once __DIR__ . '/../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$usuario_id = $_SESSION['id_usuario'];

try {
    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("DELETE FROM Usuario WHERE id_usuario = :id");
    $stmt->execute([':id' => $usuario_id]);

    session_destroy();

    echo "<script>alert('✅ Conta excluída com sucesso!'); window.location.href = '../../inicio.html';</script>";
} catch (PDOException $e) {
    echo "<script>alert('❌ Erro ao excluir conta: " . $e->getMessage() . "'); window.history.back();</script>";
}
