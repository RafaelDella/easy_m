<?php
require_once '../../../db.php';
$bd = new DB();
$conn = $bd->connect();

$stmt = $conn->prepare("INSERT INTO Teto_gasto (nome_teto, descricao, categoria, valor_teto, id_usuario) VALUES (?, ?, ?, ?, ?)");

$ok = $stmt->execute([
    $_POST['nome_teto'],
    $_POST['descricao'],
    $_POST['categoria'],
    $_POST['valor_teto'],
    $_POST['id_usuario']
]);

$msg = $ok ? "Teto cadastrado com sucesso!" : "Erro ao cadastrar teto.";
header("Location: 1-forms_teto_de_gasto.html?status=" . urlencode($msg));
