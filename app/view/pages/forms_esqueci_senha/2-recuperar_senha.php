<?php
require_once '../../../db.php';
require_once '../../../assets/src/PHPMailer.php';
require_once '../../../assets/src/SMTP.php';
require_once '../../../assets/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mensagemPopup = '';
$tipoMensagem = 'info'; // info ou erro

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $bd = new DB();
    $conn = $bd->connect();

    $stmt = $conn->prepare("SELECT id_usuario FROM Usuario WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt2 = $conn->prepare("INSERT INTO RecuperacaoSenha (email, token, expiracao) VALUES (?, ?, ?)");
        $stmt2->execute([$email, $token, $expiracao]);

        $link = "http://localhost/easy_m/app/view/pages/forms_esqueci_senha/3-redefinir_senha.php?token=$token";

        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'brunokioshi01@gmail.com';
            $mail->Password = 'bfjm joof axdq oifm';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('brunokioshi01@gmail.com', 'EasyM - Suporte');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de Senha - EasyM';
            $mail->Body = "
                <h2>Recuperação de Senha</h2>
                <p>Você solicitou a redefinição da sua senha.</p>
                <p><a href='$link'>Clique aqui para redefinir sua senha</a></p>
                <p>Este link expira em 1 hora.</p>
                <hr>
                <p style='font-size:12px'>Se você não solicitou isso, ignore este e-mail.</p>
            ";

            $mail->send();
            $mensagemPopup = "Um link de recuperação foi enviado para seu e-mail.";
        } catch (Exception $e) {
            $mensagemPopup = "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
            $tipoMensagem = 'erro';
        }
    } else {
        $mensagemPopup = "E-mail não encontrado. Verifique o endereço digitado.";
        $tipoMensagem = 'erro';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - EasyM</title>
    <link rel="stylesheet" href="../../../assets/css/pages/3-forms_login.css">
    <link rel="stylesheet" href="../../../assets/css/pages/18-recuperar_senha.css">
</head>

<body>

    <div class="form-container">
        <h2>Recuperar Senha</h2>

        <form action="2-recuperar_senha.php" method="POST">
            <label for="email">Informe seu e-mail cadastrado:</label>
            <input type="email" id="email" name="email" required>

            <button type="submit">Enviar link de recuperação</button>
        </form>

        <p class="link-cadastro">
            Lembrou sua senha? <a href="../forms_login/1-forms_login.html">Voltar ao login</a>
        </p>
    </div>

    <?php if (!empty($mensagemPopup)): ?>
        <div class="popup" id="popup">
            <div class="popup-content <?= $tipoMensagem === 'erro' ? 'erro' : '' ?>">
                <p><?= htmlspecialchars($mensagemPopup) ?></p>
                <button onclick="document.getElementById('popup').style.display='none'">OK</button>
            </div>
        </div>
        <script>
            window.onload = () => {
                document.getElementById('popup').style.display = 'flex';
            };
        </script>
    <?php endif; ?>

</body>

</html>