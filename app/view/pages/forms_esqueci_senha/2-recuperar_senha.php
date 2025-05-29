<?php
require_once '../../../db.php';

// Inclui PHPMailer manualmente
require_once '../../../assets/src/PHPMailer.php';
require_once '../../../assets/src/SMTP.php';
require_once '../../../assets/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
            $mail->Username = 'brunokioshi01@gmail.com'; // 🔒 Substitua pelo seu Gmail
            $mail->Password = 'bfjm joof axdq oifm';   // 🔒 Use a senha de app do Gmail
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
            echo "<p style='text-align:center'>Se o e-mail estiver cadastrado, você receberá um link de recuperação.</p>";
        } catch (Exception $e) {
            echo "<p style='color:red;'>Erro ao enviar o e-mail: {$mail->ErrorInfo}</p>";
        }
    } else {
        echo "<p style='text-align:center'>Se o e-mail estiver cadastrado, você receberá um link de recuperação.</p>";
    }
}
