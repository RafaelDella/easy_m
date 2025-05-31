<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("SELECT id_categoria, nome_categoria FROM CategoriaDespesa WHERE id_usuario = :id_usuario ORDER BY nome_categoria");
    $stmt->execute([':id_usuario' => $id_usuario]);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['sucesso' => true, 'categorias' => $categorias]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao listar categorias: ' . $e->getMessage()]);
    // error_log("Erro ao listar categorias: " . $e->getMessage());
}
// NADA AQUI ABAIXO!