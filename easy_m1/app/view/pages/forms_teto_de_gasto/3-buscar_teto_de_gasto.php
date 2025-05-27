<?php
require_once '../../../db.php';
$bd = new DB();
$conn = $bd->connect();

$id_usuario = $_GET['id_usuario'] ?? 1;

$stmt = $conn->prepare("SELECT * FROM Teto_gasto WHERE id_usuario = ?");
$stmt->execute([$id_usuario]);

$tetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($tetos);
