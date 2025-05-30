<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id'])) {
    // Redireciona de volta com uma mensagem de erro, ou retorna JSON se for uma requisição AJAX
    header("Location: 1-forms_despesa.php?status=error&message=Requisição inválida.");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_despesa = $_POST['id'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM Despesa WHERE id_despesa = :id_despesa AND id_usuario = :id_usuario");
    $stmt->execute(['id_despesa' => $id_despesa, 'id_usuario' => $id_usuario]);

    if ($stmt->rowCount() > 0) {
        header("Location: 1-forms_despesa.php?status=success&message=Despesa excluída com sucesso!");
    } else {
        header("Location: 1-forms_despesa.php?status=error&message=Despesa não encontrada ou você não tem permissão para excluí-la.");
    }
} catch (PDOException $e) {
    header("Location: 1-forms_despesa.php?status=error&message=Erro ao excluir despesa: " . urlencode($e->getMessage()));
    // error_log("Erro ao excluir despesa: " . $e->getMessage());
}
exit;