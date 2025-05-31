<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_categoria'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_categoria = $_POST['id_categoria'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    // É uma boa prática verificar se há despesas associadas a esta categoria.
    // Você pode:
    // 1. Impedir a exclusão se houver despesas associadas (erro)
    // 2. Definir id_categoria das despesas para NULL (se a coluna permitir NULL)
    // 3. Excluir as despesas associadas (cuidado, pode não ser desejado)
    // A chave estrangeira ON DELETE CASCADE já lida com isso se configurada.

    $stmt = $pdo->prepare("DELETE FROM CategoriaDespesa WHERE id_categoria = :id_categoria AND id_usuario = :id_usuario");
    $stmt->execute([':id_categoria' => $id_categoria, ':id_usuario' => $id_usuario]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Categoria excluída com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Categoria não encontrada ou você não tem permissão para excluí-la.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao excluir categoria: ' . $e->getMessage()]);
    // error_log("Erro ao excluir categoria: " . $e->getMessage());
}
// NADA AQUI ABAIXO!