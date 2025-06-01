<?php
session_start();
require_once __DIR__ . '/../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

try {
    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("DELETE FROM Meta WHERE id_usuario = :id_usuario");
    $stmt->execute([':id_usuario' => $id_usuario]);

    header("Location: 1-dashboard_metas.php");
} catch (PDOException $e) {
    echo "Erro ao excluir metas: " . $e->getMessage();
}
