<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../formularioulario_login/formulario_login.html');
    exit;
}

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];

// Captura dados do formularioulario
$id = $_POST['id'] ?? null;
$nome = $_POST['nome_gasto'];
$desc = $_POST['desc_gasto'] ?? null;
$categoria = $_POST['categoria_gasto'];
$valor = $_POST['valor_gasto'];
$data = $_POST['data_gasto'];
$is_imprevisto = isset($_POST['is_imprevisto']) ? 1 : 0;

try {
    if (!empty($id)) {
        // ATUALIZAÇÃO
        $stmt = $pdo->prepare("UPDATE Gasto SET 
            nome_gasto = :nome, 
            desc_gasto = :desc, 
            categoria_gasto = :categoria, 
            valor_gasto = :valor, 
            is_imprevisto = :is_imprevisto, 
            data_gasto = :data 
            WHERE id_gasto = :id AND usuario_id = :usuario_id");

        $stmt->execute([
            ':nome' => $nome,
            ':desc' => $desc,
            ':categoria' => $categoria,
            ':valor' => $valor,
            ':is_imprevisto' => $is_imprevisto,
            ':data' => $data,
            ':id' => $id,
            ':usuario_id' => $usuario_id
        ]);

        echo "<script>alert('✅ Gasto atualizado com sucesso!'); window.location.href='../extrato_page/extrato_view.php';</script>";
    } else {
        // INSERÇÃO
        $stmt = $pdo->prepare("INSERT INTO Gasto (
            nome_gasto, desc_gasto, categoria_gasto, valor_gasto, is_imprevisto, data_gasto, usuario_id
        ) VALUES (
            :nome, :desc, :categoria, :valor, :is_imprevisto, :data, :usuario_id
        )");

        $stmt->execute([
            ':nome' => $nome,
            ':desc' => $desc,
            ':categoria' => $categoria,
            ':valor' => $valor,
            ':is_imprevisto' => $is_imprevisto,
            ':data' => $data,
            ':usuario_id' => $usuario_id
        ]);

        echo "<script>alert('✅ Gasto cadastrado com sucesso!'); window.location.href='../extrato_page/extrato_view.php';</script>";
    }
} catch (PDOException $e) {
    echo "Erro ao salvar gasto: " . $e->getMessage();
}
