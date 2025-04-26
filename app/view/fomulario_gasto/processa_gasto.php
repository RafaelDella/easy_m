<?php
session_start();
require_once '../../db.php'; // Caminho relativo correto para db.php

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../formulario_login/form_login.html');
    exit;
}

// Conecta usando PDO
$db = new DB();
$pdo = $db->connect();

// Captura dados do formulário
$nome = $_POST['nome_gasto'];
$desc = $_POST['desc_gasto'] ?? null;
$categoria = $_POST['categoria_gasto'];
$valor = $_POST['valor_gasto'];
$data = $_POST['data_gasto'];
$is_imprevisto = isset($_POST['is_imprevisto']) ? 1 : 0;

// ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

try {
    $stmt = $pdo->prepare("INSERT INTO Gasto (nome_gasto, desc_gasto, categoria_gasto, valor_gasto, is_imprevisto, data_gasto, usuario_id)VALUES (:nome, :desc, :categoria, :valor, :is_imprevisto, :data, :usuario_id)");

    $stmt->execute([
        ':nome' => $nome,
        ':desc' => $desc,
        ':categoria' => $categoria,
        ':valor' => $valor,
        ':is_imprevisto' => $is_imprevisto,
        ':data' => $data,
        ':usuario_id' => $usuario_id
    ]);

    echo "<script>alert('✅ Gasto cadastrado com sucesso!'); window.location.href='../view/dashboard.php';</script>";

} catch (PDOException $e) {
    echo "Erro ao cadastrar gasto: " . $e->getMessage();
}
?>
