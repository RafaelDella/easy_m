<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../formulario_login/form_login.html');
    exit;
}

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];

// Captura o perfil selecionado
$perfil = $_POST['perfil'];

try {
    // Atualiza o campo 'perfil' na tabela Usuario
    $stmt = $pdo->prepare("UPDATE Usuario SET perfil = :perfil WHERE id = :usuario_id");
    $stmt->execute([
        ':perfil' => $perfil,
        ':usuario_id' => $usuario_id
    ]);

    echo "<script>alert('✅ Perfil financeiro atualizado com sucesso!'); window.location.href='../view/dashboard.php';</script>";

} catch (PDOException $e) {
    echo "Erro ao atualizar perfil: " . $e->getMessage();
}
?>
