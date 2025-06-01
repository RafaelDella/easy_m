<?php
session_start();
require_once __DIR__ . '/../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$id_meta = $_POST['id_meta'] ?? 0;
$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$valor_meta = $_POST['valor_meta'] ?? 0;
$previsao = $_POST['previsao_conclusao'] ?? '';

try {
    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("UPDATE Meta SET 
        titulo = :titulo,
        descricao = :descricao,
        categoria = :categoria,
        valor_meta = :valor_meta,
        previsao_conclusao = :previsao
        WHERE id_meta = :id AND id_usuario = :id_usuario");

    $stmt->execute([
        ':titulo' => $titulo,
        ':descricao' => $descricao,
        ':categoria' => $categoria,
        ':valor_meta' => $valor_meta,
        ':previsao' => $previsao,
        ':id' => $id_meta,
        ':id_usuario' => $id_usuario
    ]);

    echo json_encode(['sucesso' => true]);
} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}
