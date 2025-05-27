<?php
require_once '../../db.php';
$bd = new DB();
$conn = $bd->connect();

$stmt = $conn->prepare("DELETE FROM Divida WHERE id_divida = ?");
$stmt->execute([$_GET['id']]);

header("Location: index.php?status=" . urlencode("Dívida excluída."));
