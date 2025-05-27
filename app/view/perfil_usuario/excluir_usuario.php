<?php
session_start();
require_once '../../db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
<<<<<<< Updated upstream
    header("Location: ../formularioulario_login/formulario_login.html");
=======
    header("Location: ../forms_login/form_login.html");
>>>>>>> Stashed changes
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

$db = new DB();
$pdo = $db->connect();

try {
    // Remove o usuário
    $stmt = $pdo->prepare("DELETE FROM Usuario WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);

    // Destroi a sessão
    session_destroy();

    echo "<script>
        alert('✅ Conta excluída com sucesso!');
<<<<<<< Updated upstream
        window.location.href = '../formularioulario_login/formulario_login.html';
=======
        window.location.href = '../forms_login/form_login.html';
>>>>>>> Stashed changes
    </script>";
} catch (PDOException $e) {
    echo "Erro ao excluir conta: " . $e->getMessage();
}
