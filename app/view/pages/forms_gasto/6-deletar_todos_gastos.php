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

try {
    $stmt = $pdo->prepare("DELETE FROM Gasto WHERE id_usuario = :id_usuario");
    $stmt->execute(['id_usuario' => $id_usuario]);

    echo "<script>
        alert('✅ Todos os gastos foram excluídos com sucesso!');
        window.location.href='1-forms_gasto.php';
    </script>";
    exit;
} catch (PDOException $e) {
    echo "<script>
        alert('❌ Erro ao excluir gastos: Por favor, tente novamente mais tarde.');
        window.location.href='1-forms_gasto.php';
    </script>";
    // error_log("Erro ao excluir todos os gastos do usuário {$id_usuario}: " . $e->getMessage());
    exit;
}
?>