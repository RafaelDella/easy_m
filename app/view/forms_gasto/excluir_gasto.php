<?php
session_start();
require_once '../../db.php';

// Verifica se o usuário está logado e se o ID foi enviado
if (!isset($_SESSION['usuario_id']) || !isset($_POST['id'])) {
    header("Location: ../forms_login/forms_login.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$id = $_POST['id'];

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM Gasto WHERE id_gasto = :id AND usuario_id = :usuario_id");
    $stmt->execute(['id' => $id, 'usuario_id' => $usuario_id]);

    echo "<script>
        alert('✅ Gasto excluído com sucesso!');
        window.location.href='../extrato_page/extrato_view.php';
    </script>";
} catch (PDOException $e) {
    echo "Erro ao excluir gasto: " . $e->getMessage();
}
