<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id = $_GET['id'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("SELECT * FROM Divida WHERE id_divida = :id AND id_usuario = :id_usuario");
    $stmt->execute(['id' => $id, 'id_usuario' => $id_usuario]);
    $divida = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($divida) {
        echo json_encode(['sucesso' => true, 'divida' => $divida]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dívida não encontrada ou você não tem permissão para acessá-la.']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao buscar dívida.']);
    // error_log("Erro ao buscar dívida: " . $e->getMessage());
}
