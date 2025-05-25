<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../formulario_login/form_login.html");
    exit;
}

require_once '../../db.php';

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];

// Deletar todas as entradas do usuário logado
$stmt = $pdo->prepare("DELETE FROM Entrada WHERE id_usuario = :usuario_id");
$stmt->execute(['usuario_id' => $usuario_id]);

// Redirecionar após exclusão
header("Location: formulario_entrada.php");
exit;
?>
