<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['nome_categoria'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nome_categoria = trim($_POST['nome_categoria']);

if (empty($nome_categoria)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O nome da categoria não pode ser vazio.']);
    exit;
}

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    // Verifica se a categoria já existe para este usuário
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM CategoriaDespesa WHERE nome_categoria = :nome_categoria AND id_usuario = :id_usuario");
    $stmtCheck->execute([':nome_categoria' => $nome_categoria, ':id_usuario' => $id_usuario]);
    if ($stmtCheck->fetchColumn() > 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Você já possui uma categoria com este nome.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO CategoriaDespesa (nome_categoria, id_usuario) VALUES (:nome_categoria, :id_usuario)");
    $stmt->execute([':nome_categoria' => $nome_categoria, ':id_usuario' => $id_usuario]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Categoria cadastrada com sucesso!', 'id_categoria' => $pdo->lastInsertId(), 'nome_categoria' => $nome_categoria]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar categoria: ' . $e->getMessage()]);
    // error_log("Erro ao cadastrar categoria: " . $e->getMessage());
}
// NADA AQUI ABAIXO!