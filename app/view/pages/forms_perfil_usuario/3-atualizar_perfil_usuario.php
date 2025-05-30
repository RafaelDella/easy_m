<?php
session_start();
require_once __DIR__ . '/../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$usuario_id = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            WHERE id_usuario = :id");

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
            echo "<script>alert('✅ Perfil atualizado com sucesso!'); window.location.href = '1-forms_perfil_usuario.php';</script>";
        } else {
            echo "<script>alert('⚠️ Nenhuma alteração foi feita.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('❌ Erro ao atualizar perfil: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('❌ Método inválido.'); window.history.back();</script>";
}
