<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_categoria']) || !isset($_POST['nome_categoria'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_categoria = $_POST['id_categoria'];
$nome_categoria = trim($_POST['nome_categoria']);

if (empty($nome_categoria)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'O nome da categoria não pode ser vazio.']);
    exit;
}

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    // Verifica se o novo nome da categoria já existe para este usuário (excluindo a categoria atual)
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM CategoriaDespesa WHERE nome_categoria = :nome_categoria AND id_usuario = :id_usuario AND id_categoria != :id_categoria");
    $stmtCheck->execute([':nome_categoria' => $nome_categoria, ':id_usuario' => $id_usuario, ':id_categoria' => $id_categoria]);
    if ($stmtCheck->fetchColumn() > 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Você já possui outra categoria com este nome.']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE CategoriaDespesa SET nome_categoria = :nome_categoria WHERE id_categoria = :id_categoria AND id_usuario = :id_usuario");
    $stmt->execute([':nome_categoria' => $nome_categoria, ':id_categoria' => $id_categoria, ':id_usuario' => $id_usuario]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Categoria atualizada com sucesso!', 'nome_categoria' => $nome_categoria]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Categoria não encontrada ou nenhuma alteração realizada.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar categoria: ' . $e->getMessage()]);
    // error_log("Erro ao atualizar categoria: " . $e->getMessage());
}
// NADA AQUI ABAIXO!