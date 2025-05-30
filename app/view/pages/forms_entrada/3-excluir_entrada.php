<?php
session_start();

// Verifica se o usuário está logado e se os dados foram enviados
// 'id_usuario' é usado para consistência com o outro arquivo
if (!isset($_SESSION['id_usuario']) || !isset($_POST['id'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$id = $_POST['id'];
$id_usuario = $_SESSION['id_usuario']; // Alterado para 'id_usuario' para consistência

require_once __DIR__ . '../../../../db.php'; // Caminho ajustado para consistência

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM Entrada WHERE id_entrada = :id AND id_usuario = :id_usuario");
    $stmt->execute(['id' => $id, 'id_usuario' => $id_usuario]);

    echo "<script>
        alert('✅ Entrada excluída com sucesso!');
        window.location.href='1-forms_entrada.php'; // Redireciona para a mesma página de formulário de entrada
    </script>";
    exit;
} catch (PDOException $e) {
    // Melhor tratamento de erro para o usuário final
    echo "<script>
        alert('❌ Erro ao excluir entrada: Por favor, tente novamente mais tarde.');
        window.location.href='1-forms_entrada.php'; // Redireciona de volta para a página de formulário
    </script>";
    // Em um ambiente de produção, você registraria $e->getMessage() em um arquivo de log
    // error_log("Erro ao excluir entrada: " . $e->getMessage());
    exit;
}
?>