<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$id_divida = $_POST['id'];
$id_usuario = $_SESSION['id_usuario'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM Divida WHERE id_divida = :id AND id_usuario = :id_usuario");
    $stmt->execute(['id' => $id_divida, 'id_usuario' => $id_usuario]);

    echo "<script>
        alert('✅ Dívida excluída com sucesso!');
        window.location.href='1-forms_divida.php';
    </script>";
    exit;
} catch (PDOException $e) {
    echo "<script>
        alert('❌ Erro ao excluir dívida: Por favor, tente novamente mais tarde.');
        window.location.href='1-forms_divida.php';
    </script>";
    // error_log("Erro ao excluir dívida: " . $e->getMessage());
    exit;
}
