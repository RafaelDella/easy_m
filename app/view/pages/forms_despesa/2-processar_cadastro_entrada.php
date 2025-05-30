<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    $nome_despesa     = $_POST['nome_despesa'] ?? '';
    $descricao        = $_POST['descricao'] ?? '';
    $valor_despesa    = $_POST['valor_despesa'] ?? 0;
    $data_vencimento  = $_POST['data_vencimento'] ?? '';
    $id_categoria     = $_POST['id_categoria'] ?? '';

    if (empty($nome_despesa) || empty($valor_despesa) || empty($data_vencimento) || empty($id_categoria)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos obrigatórios devem ser preenchidos.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO Despesa (nome_despesa, descricao, valor_despesa, data_vencimento, id_usuario, id_categoria) VALUES (:nome_despesa, :descricao, :valor_despesa, :data_vencimento, :id_usuario, :id_categoria)");
    $stmt->execute([
        ':nome_despesa'    => $nome_despesa,
        ':descricao'       => $descricao,
        ':valor_despesa'   => $valor_despesa,
        ':data_vencimento' => $data_vencimento,
        ':id_usuario'      => $id_usuario,
        ':id_categoria'    => $id_categoria
    ]);

    echo json_encode(['sucesso' => true, 'mensagem' => 'Despesa cadastrada com sucesso!']);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar despesa: ' . $e->getMessage()]);
    // error_log("Erro ao cadastrar despesa: " . $e->getMessage());
}