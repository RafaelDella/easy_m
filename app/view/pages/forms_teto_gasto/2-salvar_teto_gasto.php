<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

require_once __DIR__ . '../../../../db.php';
$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario'];

$categoria = mb_strtolower(trim($_POST['categoria']), 'UTF-8');
$nome_teto = trim($_POST['nome_teto'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$valor_teto = floatval($_POST['valor_teto'] ?? 0);

if ($categoria === '' || $nome_teto === '' || $valor_teto <= 0) {
    echo "<script>alert('Preencha todos os campos obrigatórios.'); history.back();</script>";
    exit;
}

$stmt = $pdo->prepare("SELECT id_teto FROM Teto_gasto WHERE id_usuario = ? AND categoria = ?");
$stmt->execute([$id_usuario, $categoria]);

if ($stmt->rowCount() > 0) {
    $update = $pdo->prepare("UPDATE Teto_gasto SET nome_teto = ?, descricao = ?, valor_teto = ? WHERE id_usuario = ? AND categoria = ?");
    $update->execute([$nome_teto, $descricao, $valor_teto, $id_usuario, $categoria]);
} else {
    $insert = $pdo->prepare("INSERT INTO Teto_gasto (nome_teto, descricao, categoria, valor_teto, id_usuario) VALUES (?, ?, ?, ?, ?)");
    $insert->execute([$nome_teto, $descricao, $categoria, $valor_teto, $id_usuario]);
}

header("Location: 1-forms_teto_gasto.php?categoria=" . urlencode($categoria));
exit;
