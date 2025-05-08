<?php
session_start();
require_once '../../db.php';

// Força o tipo de retorno como JSON
header('Content-Type: application/json');

// Lê os dados enviados via JSON
$dados = json_decode(file_get_contents("php://input"), true);

// Verifica se usuário e senha foram enviados
$usuario = $dados['usuario'] ?? '';
$senha = $dados['senha'] ?? '';

// Conecta ao banco
$db = new DB();
$pdo = $db->connect();

$stmt = $pdo->prepare("SELECT id, nome, senha FROM Usuario WHERE email = :usuario OR usuario = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($senha, $row['senha'])) {
        $_SESSION['usuario_id'] = $row['id'];
        $_SESSION['usuario_nome'] = $row['nome'];

        echo json_encode(["sucesso" => true]);
        exit;
    } else {
        echo json_encode(["sucesso" => false, "mensagem" => "Senha incorreta."]);
        exit;
    }
} else {
    echo json_encode(["sucesso" => false, "mensagem" => "Usuário não encontrado."]);
    exit;
}
