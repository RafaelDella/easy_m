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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nome_gasto = trim($_POST['nome_gasto'] ?? '');
    $desc_gasto = trim($_POST['desc_gasto'] ?? '');
    $valor_gasto = filter_var($_POST['valor_gasto'] ?? 0, FILTER_VALIDATE_FLOAT);
    $categoria_gasto = $_POST['categoria_gasto'] ?? '';
    $is_imprevisto = isset($_POST['is_imprevisto']) ? 1 : 0;
    $data_gasto = $_POST['data_gasto'] ?? '';

    if (empty($id) || empty($nome_gasto) || empty($desc_gasto) || $valor_gasto === false || $valor_gasto <= 0 || empty($categoria_gasto) || empty($data_gasto)) {
        echo "<script>alert('❌ Por favor, preencha todos os campos corretamente para atualizar o gasto.'); window.location.href='1-forms_gasto.php';</script>";
        exit;
    }

    try {
        $sql = "UPDATE Gasto SET nome_gasto = :nome_gasto, desc_gasto = :desc_gasto, categoria_gasto = :categoria_gasto, valor_gasto = :valor_gasto, is_imprevisto = :is_imprevisto, data_gasto = :data_gasto
                 WHERE id_gasto = :id AND id_usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome_gasto' => $nome_gasto,
            ':desc_gasto' => $desc_gasto,
            ':categoria_gasto' => $categoria_gasto,
            ':valor_gasto' => $valor_gasto,
            ':is_imprevisto' => $is_imprevisto,
            ':data_gasto' => $data_gasto,
            ':id' => $id,
            ':id_usuario' => $id_usuario
        ]);

        echo "<script>
            alert('✅ Gasto atualizado com sucesso!');
            window.location.href = '1-forms_gasto.php';
        </script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>
            alert('❌ Erro ao atualizar gasto: Por favor, tente novamente mais tarde.');
            window.location.href='1-forms_gasto.php';
        </script>";
        // error_log("Erro ao atualizar gasto: " . $e->getMessage());
        exit;
    }
} else {
    echo "<script>alert('Requisição inválida.'); window.location.href='1-forms_gasto.php';</script>";
    exit;
}
?>