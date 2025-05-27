<?php
require_once '../../../db.php';
$bd = new DB();
$conn = $bd->connect();

$stmt = $conn->prepare("UPDATE Teto_gasto SET nome_teto = ?, descricao = ?, categoria = ?, valor_teto = ? WHERE id_teto = ? AND id_usuario = ?");

$ok = $stmt->execute([
    $_POST['nome_teto'],
    $_POST['descricao'],
    $_POST['categoria'],
    $_POST['valor_teto'],
    $_POST['id_teto'],
    $_POST['id_usuario']
]);

$msg = $ok ? "Teto atualizado com sucesso!" : "Erro ao atualizar teto.";
header("Location: 1-forms_teto_de_gasto.html?status=" . urlencode($msg));
