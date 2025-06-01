<?php
// Arquivo: app/view/pages/includes/notificacoes.php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    echo json_encode([]);
    exit;
}

$hoje = new DateTime();
$limite = (clone $hoje)->modify('+1000 days')->format('Y-m-d');

$notificacoes = [];

// Notificações de despesas
$stmt = $pdo->prepare("SELECT nome_despesa AS nome, data_vencimento FROM Despesa WHERE id_usuario = :id AND data_vencimento BETWEEN CURDATE() AND :limite");
$stmt->execute(['id' => $id_usuario, 'limite' => $limite]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dias = (new DateTime($row['data_vencimento']))->diff($hoje)->days;
    $notificacoes[] = ['tipo' => 'Despesa', 'nome' => $row['nome'], 'dias' => $dias];
}

// Notificações de dívidas
$stmt = $pdo->prepare("SELECT nome_divida AS nome, data_vencimento FROM Divida WHERE id_usuario = :id AND data_vencimento BETWEEN CURDATE() AND :limite");
$stmt->execute(['id' => $id_usuario, 'limite' => $limite]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dias = (new DateTime($row['data_vencimento']))->diff($hoje)->days;
    $notificacoes[] = ['tipo' => 'Dívida', 'nome' => $row['nome'], 'dias' => $dias];
}

echo json_encode($notificacoes);
exit;
