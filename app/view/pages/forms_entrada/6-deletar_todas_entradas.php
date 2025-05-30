<?php
session_start();

// 'id_usuario' é usado para consistência com os outros arquivos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html"); // Caminho ajustado
    exit;
}

require_once __DIR__ . '../../../../db.php'; // Caminho ajustado para consistência

$db = new DB();
$pdo = $db->connect();

$id_usuario = $_SESSION['id_usuario']; // Alterado para 'id_usuario' para consistência

try {
    // Deletar todas as entradas do usuário logado
    $stmt = $pdo->prepare("DELETE FROM Entrada WHERE id_usuario = :id_usuario");
    $stmt->execute(['id_usuario' => $id_usuario]);

    // Redirecionar após exclusão com mensagem de sucesso
    echo "<script>
        alert('✅ Todas as entradas foram excluídas com sucesso!');
        window.location.href='1-forms_entrada.php'; // Redireciona para a página de formulário de entrada
    </script>";
    exit;
} catch (PDOException $e) {
    // Tratamento de erro aprimorado
    echo "<script>
        alert('❌ Erro ao excluir entradas: Por favor, tente novamente mais tarde.');
        window.location.href='1-forms_entrada.php'; // Redireciona de volta para a página de formulário
    </script>";
    // Em um ambiente de produção, registre o erro:
    // error_log("Erro ao excluir todas as entradas do usuário {$id_usuario}: " . $e->getMessage());
    exit;
}
?>