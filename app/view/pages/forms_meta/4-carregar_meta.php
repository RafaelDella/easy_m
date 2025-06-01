<?php
session_start();
require_once __DIR__ . '/../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_meta = $_GET['id'] ?? 0;

try {
    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("SELECT * FROM meta WHERE id_meta = :id AND id_usuario = :id_usuario");
    $stmt->execute([
        ':id' => $id_meta,
        ':id_usuario' => $id_usuario
    ]);

    $meta = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['sucesso' => true, 'meta' => $meta]);
} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
