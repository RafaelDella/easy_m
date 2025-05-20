<?php
session_start();
require_once '../../../app/db.php';

// Verifica se o usuário está logado e se os dados foram enviados
if (!isset($_SESSION['usuario_id']) || !isset($_POST['id']) || $_POST['tipo'] !== 'receita') {
    header("Location: ../../formularioulario_login/formulario_login.html");
    exit;
}

$id = $_POST['id'];
$usuario_id = $_SESSION['usuario_id'];

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM Entrada WHERE id_entrada = :id AND usuario_id = :usuario_id");
    $stmt->execute(['id' => $id, 'usuario_id' => $usuario_id]);

    echo "<script>
        alert('✅ Entrada excluída com sucesso!');
        window.location.href='../extrato_page/extrato_view.php';
    </script>";
} catch (PDOException $e) {
    echo "Erro ao excluir entrada: " . $e->getMessage();
}
