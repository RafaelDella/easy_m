<?php
session_start();
require_once '../../../app/db.php';

$_SESSION['usuario_id'] = 5;

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    echo json_encode(['sucesso' => false]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id = $_GET['id'];

$db = new DB();
$pdo = $db->connect();

$stmt = $pdo->prepare("SELECT * FROM Entrada WHERE id_entrada = :id AND id_usuario = :usuario_id");
$stmt->execute(['id' => $id, 'usuario_id' => $usuario_id]);
$entrada = $stmt->fetch(PDO::FETCH_ASSOC);

if ($entrada) {
    echo json_encode(['sucesso' => true, 'entrada' => $entrada]);
} else {
    echo json_encode(['sucesso' => false]);
}
