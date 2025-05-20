<?php
session_start();
require_once '../../../app/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../formularioulario_login/formulario_login.html");
    exit;
}

$db = new DB();
$pdo = $db->connect();
$usuario_id = $_SESSION['usuario_id'];

$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$categoria = $_POST['categoria'];
$data_entrada = $_POST['data_entrada'];

if (!empty($_POST['id'])) {
    // ATUALIZAÇÃO
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("UPDATE Entrada 
            SET descricao = :descricao, valor = :valor, categoria = :categoria, data_entrada = :data 
            WHERE id_entrada = :id AND usuario_id = :usuario_id");

        $stmt->execute([
            'descricao' => $descricao,
            'valor' => $valor,
            'categoria' => $categoria,
            'data' => $data_entrada,
            'id' => $id,
            'usuario_id' => $usuario_id
        ]);

        echo "<script>
            alert('✅ Entrada atualizada com sucesso!');
            window.location.href='../extrato_page/extrato_view.php';
        </script>";
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar entrada: " . $e->getMessage();
    }
} else {
    // INSERÇÃO
    try {
        $stmt = $pdo->prepare("INSERT INTO Entrada (descricao, valor, categoria, data_entrada, usuario_id) 
            VALUES (:descricao, :valor, :categoria, :data_entrada, :usuario_id)");

        $stmt->execute([
            'descricao' => $descricao,
            'valor' => $valor,
            'categoria' => $categoria,
            'data_entrada' => $data_entrada,
            'usuario_id' => $usuario_id
        ]);

        echo "<script>
            alert('✅ Entrada registrada com sucesso!');
            window.location.href='../extrato_page/extrato_view.php';
        </script>";
        exit;
    } catch (PDOException $e) {
        echo "Erro ao registrar entrada: " . $e->getMessage();
    }
}
