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

    // Campos de senha (opcional)
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    try {
        $db = new DB();
        $pdo = $db->connect();

        // Atualização dos dados do perfil
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

        // Tratamento da alteração de senha, se preenchida
        if (!empty($senha_atual) || !empty($nova_senha) || !empty($confirmar_senha)) {
            if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
                throw new Exception("Todos os campos de senha devem ser preenchidos.");
            }

            if ($nova_senha !== $confirmar_senha) {
                throw new Exception("A nova senha e a confirmação não coincidem.");
            }

            // Verifica se a senha atual está correta
            $stmtSenha = $pdo->prepare("SELECT senha FROM Usuario WHERE id_usuario = :id");
            $stmtSenha->execute([':id' => $usuario_id]);
            $senhaHash = $stmtSenha->fetchColumn();

            if (!$senhaHash || !password_verify($senha_atual, $senhaHash)) {
                throw new Exception("Senha atual incorreta.");
            }

            // Atualiza a nova senha
            $novaSenhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $stmtUpdateSenha = $pdo->prepare("UPDATE Usuario SET senha = :senha WHERE id_usuario = :id");
            $stmtUpdateSenha->execute([
                ':senha' => $novaSenhaHash,
                ':id' => $usuario_id
            ]);
        }

        echo "<script>alert('✅ Perfil atualizado com sucesso!'); window.location.href = '1-forms_perfil_usuario.php';</script>";

    } catch (Exception $e) {
        echo "<script>alert('❌ Erro: " . $e->getMessage() . "'); window.history.back();</script>";
    } catch (PDOException $e) {
        echo "<script>alert('❌ Erro no banco de dados: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('❌ Método inválido.'); window.history.back();</script>";
}
