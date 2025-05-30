<?php
session_start();
require_once __DIR__ . '../../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado']);
    exit;
}

$usuario_id = $_SESSION['id_usuario'];

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("SELECT id_usuario AS id, nome, email, usuario, cpf, escolaridade, data_nascimento FROM Usuario WHERE id_usuario = :id");
    $stmt->execute([':id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        echo json_encode(['sucesso' => true, 'usuario' => $usuario]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
    }
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
}