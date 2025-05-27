<?php
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $senha = $_POST['senha'];
    $confirmar = $_POST['confirmar'];

    if ($senha !== $confirmar) {
        echo "As senhas não coincidem. <a href='javascript:history.back()'>Voltar</a>";
        exit;
    }

    if (strlen($senha) < 8) {
        echo "A senha deve ter no mínimo 8 caracteres.";
        exit;
    }

    $bd = new DB();
    $conn = $bd->connect();

    // Busca o e-mail pelo token
    $stmt = $conn->prepare("SELECT email FROM RecuperacaoSenha WHERE token = ? AND expiracao > NOW()");
    $stmt->execute([$token]);

    if ($stmt->rowCount() === 0) {
        echo "Token inválido ou expirado.";
        exit;
    }

    $email = $stmt->fetchColumn();

    // Criptografa a nova senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Atualiza a senha
    $stmt2 = $conn->prepare("UPDATE Usuario SET senha = ? WHERE email = ?");
    $stmt2->execute([$senhaHash, $email]);

    // Remove o token
    $conn->prepare("DELETE FROM RecuperacaoSenha WHERE token = ?")->execute([$token]);

    echo "Senha atualizada com sucesso. <a href='forms_login.html'>Fazer login</a>";
}
?>
