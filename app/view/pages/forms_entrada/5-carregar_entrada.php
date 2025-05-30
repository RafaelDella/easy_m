<?php
session_start();

// Removendo a definição fixa de 'usuario_id', pois a sessão deve ser estabelecida no login.
// $_SESSION['usuario_id'] = 5; 

// 'id_usuario' é usado para consistência com os outros arquivos
if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario']; // Alterado para 'id_usuario' para consistência
$id = $_GET['id'];

require_once __DIR__ . '../../../../db.php'; // Caminho ajustado para consistência

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("SELECT * FROM Entrada WHERE id_entrada = :id AND id_usuario = :id_usuario");
    $stmt->execute(['id' => $id, 'id_usuario' => $id_usuario]); // Parâmetro ajustado para 'id_usuario'
    $entrada = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($entrada) {
        echo json_encode(['sucesso' => true, 'entrada' => $entrada]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Entrada não encontrada ou você não tem permissão para acessá-la.']);
    }
} catch (PDOException $e) {
    // Em um ambiente de produção, logar o erro detalhado e mostrar uma mensagem genérica.
    // Para desenvolvimento, pode-se incluir $e->getMessage() para depuração.
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao buscar entrada.']);
    // error_log("Erro ao buscar entrada: " . $e->getMessage());
}
?>