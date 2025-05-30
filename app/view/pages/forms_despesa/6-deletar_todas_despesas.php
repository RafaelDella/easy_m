<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: 1-forms_despesa.php?status=error&message=Usuário não logado.");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    // Atenção: Esta operação é IRREVERSÍVEL!
    $stmt = $pdo->prepare("DELETE FROM Despesa WHERE id_usuario = :id_usuario");
    $stmt->execute(['id_usuario' => $id_usuario]);

    header("Location: 1-forms_despesa.php?status=success&message=Todas as despesas foram excluídas com sucesso!");
} catch (PDOException $e) {
    header("Location: 1-forms_despesa.php?status=error&message=Erro ao excluir todas as despesas: " . urlencode($e->getMessage()));
    // error_log("Erro ao excluir todas as despesas: " . $e->getMessage());
}
exit;