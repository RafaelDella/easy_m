<?php
session_start();
require_once '../../db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../formulario_login/form_login.html");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Verifica se os dados foram enviados corretamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do formulário
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $escolaridade = $_POST['escolaridade'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';

    try {
        $db = new DB();
        $pdo = $db->connect();

        $stmt = $pdo->prepare("UPDATE Usuario SET 
            nome = :nome, 
            email = :email, 
            usuario = :usuario, 
            cpf = :cpf, 
            escolaridade = :escolaridade, 
            data_nascimento = :data_nascimento 
            WHERE id = :id");

        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':usuario' => $usuario,
            ':cpf' => $cpf,
            ':escolaridade' => $escolaridade,
            ':data_nascimento' => $data_nascimento,
            ':id' => $usuario_id
        ]);

        if ($stmt->rowCount() > 0) {
            echo "<script>
                alert('✅ Perfil atualizado com sucesso!');
                window.location.href = '../dashboard.php';
            </script>";
        } else {
            echo "<script>
                alert('⚠️ Nenhuma alteração foi feita.');
                window.history.back();
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>
            alert('❌ Erro ao atualizar perfil: " . $e->getMessage() . "');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>
        alert('❌ Método de requisição inválido.');
        window.history.back();
    </script>";
}
?>
