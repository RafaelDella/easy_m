<?php
require_once '../../db.php';

if (!isset($_GET['token'])) {
    die("Token inválido.");
}

$token = $_GET['token'];
$bd = new DB();
$conn = $bd->connect();

// Verifica o token e validade
$stmt = $conn->prepare("SELECT email FROM RecuperacaoSenha WHERE token = ? AND expiracao > NOW()");
$stmt->execute([$token]);

if ($stmt->rowCount() === 0) {
    die("Token inválido ou expirado.");
}

$email = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="formulario_login.css">
</head>
<body>
    <div class="formulario-container">
        <h2>Redefinir Senha</h2>
        <formulario action="atualizar_senha.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <label for="senha">Nova Senha:</label>
            <input type="password" name="senha" required minlength="8">
            <label for="confirmar">Confirmar Senha:</label>
            <input type="password" name="confirmar" required minlength="8">
            <button type="submit">Atualizar Senha</button>
        </formulario>
    </div>
</body>
</html>
