<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_despesa = $_GET['id'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    // Buscar a despesa e o nome da categoria
    $stmt = $pdo->prepare("SELECT d.*, cd.nome_categoria FROM Despesa d JOIN CategoriaDespesa cd ON d.id_categoria = cd.id_categoria WHERE d.id_despesa = :id_despesa AND d.id_usuario = :id_usuario");
    $stmt->execute(['id_despesa' => $id_despesa, 'id_usuario' => $id_usuario]);
    $despesa = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($despesa) {
        echo json_encode(['sucesso' => true, 'despesa' => $despesa]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Despesa não encontrada ou você não tem permissão para acessá-la.']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao buscar despesa.']);
    // error_log("Erro ao buscar despesa: " . $e->getMessage());
}