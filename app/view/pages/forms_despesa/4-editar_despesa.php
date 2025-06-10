<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id_despesa'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_despesa = $_POST['id_despesa'];

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

    $stmt = $pdo->prepare("UPDATE Despesa SET nome_despesa = :nome_despesa, descricao = :descricao, valor_despesa = :valor_despesa, data_vencimento = :data_vencimento, id_categoria = :id_categoria WHERE id_despesa = :id_despesa AND id_usuario = :id_usuario");
    $stmt->execute([
        ':nome_despesa'    => $nome_despesa,
        ':descricao'       => $descricao,
        ':valor_despesa'   => $valor_despesa,
        ':data_vencimento' => $data_vencimento,
        ':id_categoria'    => $id_categoria,
        ':id_despesa'      => $id_despesa,
        ':id_usuario'      => $id_usuario
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Despesa atualizada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Despesa não encontrada ou nenhuma alteração realizada.']);
    }
    exit;

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar despesa: ' . $e->getMessage()]);
    // error_log("Erro ao atualizar despesa: " . $e->getMessage());
}