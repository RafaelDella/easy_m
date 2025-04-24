<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo ao EasyM</title>
</head>
<body>
    <h1>Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>!</h1>
    <p>Login realizado com sucesso. Esta é sua página principal.</p>
    <a href="logout.php">Sair</a>
</body>
</html>
