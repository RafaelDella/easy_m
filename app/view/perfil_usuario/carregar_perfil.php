<?php
session_start();
require_once '../../db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("SELECT id, nome, email, usuario, cpf, escolaridade, data_nascimento FROM Usuario WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode(['sucesso' => true, 'usuario' => $usuario]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
