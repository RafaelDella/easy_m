<?php
require_once '../../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($senha !== $confirmar) {
        echo "<p style='color:red;'>As senhas não coincidem.</p><a href='javascript:history.back()'>Voltar</a>";
        exit;
    }

    if (strlen($senha) < 8) {
        echo "<p style='color:red;'>A senha deve ter no mínimo 8 caracteres.</p><a href='javascript:history.back()'>Voltar</a>";
        exit;
    }

    $bd = new DB();
    $conn = $bd->connect();

    // Verifica token válido
    $stmt = $conn->prepare("SELECT email FROM RecuperacaoSenha WHERE token = ? AND expiracao > NOW()");
    $stmt->execute([$token]);

    if ($stmt->rowCount() === 0) {
        echo "<p style='color:red;'>Token inválido ou expirado.</p>";
        exit;
    }

    $email = $stmt->fetchColumn();

    // Criptografa nova senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Atualiza senha
    $stmt2 = $conn->prepare("UPDATE Usuario SET senha = ? WHERE email = ?");
    $stmt2->execute([$senhaHash, $email]);

    // Remove token
    $conn->prepare("DELETE FROM RecuperacaoSenha WHERE token = ?")->execute([$token]);

    echo <<<HTML
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>Senha Redefinida - EasyM</title>
        <link rel="stylesheet" href="../../../assets/css/pages/19-popup_senha_sucesso.css">
    </head>
    <body>
        <div class="popup" id="popup">
            <div class="popup-content">
                <p>Senha atualizada com sucesso!</p>
                <a href="../forms_login/1-forms_login.html" class="botao">Fazer login</a>
            </div>
        </div>
        <script>
            document.getElementById('popup').style.display = 'flex';
        </script>
    </body>
    </html>
    HTML;
}
