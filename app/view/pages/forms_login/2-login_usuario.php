<?php
session_start();
require_once '../../../db.php';

header('Content-Type: application/json');

$dados = json_decode(file_get_contents("php://input"), true);

$usuario = $dados['usuario'] ?? '';
$senha = $dados['senha'] ?? '';

$db = new DB();
$pdo = $db->connect();

$stmt = $pdo->prepare("SELECT id_usuario, nome, senha FROM Usuario WHERE email = :usuario OR usuario = :usuario");
$stmt->bindParam(':usuario', $usuario);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($senha, $row['senha'])) {
        $_SESSION['id_usuario'] = $row['id_usuario'];
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
?>
