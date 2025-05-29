<?php
require_once '../../../db.php';

if (!isset($_GET['token'])) {
    die("Token inválido.");
}

$token = $_GET['token'];
$bd = new DB();
$conn = $bd->connect();

// Verifica se o token é válido e ainda não expirou
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
    <link rel="stylesheet" href="../../../assets/css/pages/3-forms_login.css">
</head>

<body>
    <div class="form-container">
        <h2>Redefinir Senha</h2>
        <form action="4-atualizar_senha.php" method="POST" onsubmit="return validarSenhas();">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <label for="senha">Nova Senha:</label>
            <input type="password" id="senha" name="senha" required minlength="8">
            <label for="confirmar">Confirmar Senha:</label>
            <input type="password" id="confirmar" name="confirmar" required minlength="8">
            <button type="submit">Atualizar Senha</button>
        </form>
        <p id="erro-senha" style="color: red;"></p>
    </div>

    <script>
        function validarSenhas() {
            const senha = document.getElementById('senha').value;
            const confirmar = document.getElementById('confirmar').value;
            const erro = document.getElementById('erro-senha');

            if (senha !== confirmar) {
                erro.textContent = "As senhas não coincidem.";
                return false;
            }
            return true;
        }
    </script>
</body>

</html>