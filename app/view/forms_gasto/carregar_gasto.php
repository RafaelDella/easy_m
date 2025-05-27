<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['id'])) {
    echo json_encode(['sucesso' => false]);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id = $_GET['id'];

$db = new DB();
$pdo = $db->connect();

$stmt = $pdo->prepare("SELECT * FROM Gasto WHERE id_gasto = :id AND usuario_id = :usuario_id");
$stmt->execute(['id' => $id, 'usuario_id' => $usuario_id]);
$gasto = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'sucesso' => $gasto ? true : false,
    'gasto' => $gasto
]);
