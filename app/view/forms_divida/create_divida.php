<?php
require_once '../../db.php';
$bd = new DB();
$conn = $bd->connect();

$stmt = $conn->prepare("
    INSERT INTO Divida (nome_divida, taxa_divida, categoria_divida, data_divida, usuario_id)
    VALUES (?, ?, ?, ?, ?)
");

$ok = $stmt->execute([
    $_POST['nome_divida'],
    $_POST['taxa_divida'],
    $_POST['categoria_divida'],
    $_POST['data_divida'],
    $_POST['usuario_id']
]);

$msg = $ok ? "Dívida criada com sucesso!" : "Erro ao criar dívida.";
header("Location: index.php?status=" . urlencode($msg));
