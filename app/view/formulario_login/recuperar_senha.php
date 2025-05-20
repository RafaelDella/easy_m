<?php
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $bd = new DB();
    $conn = $bd->connect();

    // Verifica se o e-mail está cadastrado
    $stmt = $conn->prepare("SELECT id FROM Usuario WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        // Gera token e salva
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt2 = $conn->prepare("INSERT INTO RecuperacaoSenha (email, token, expiracao) VALUES (?, ?, ?)");
        $stmt2->execute([$email, $token, $expiracao]);

        // Link de redefinição
        $link = "http://localhost/easy_m/app/view/formularioulario_login/redefinir_senha.php?token=$token";


        // Simulação visual
        echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; background: #f0f8ff; padding: 20px; border: 1px solid #ccc; border-radius: 8px;'>";

        echo "<h2>Recuperação de Senha - EasyM</h2>";
        echo "<p>Você solicitou a redefinição de sua senha.</p>";
        echo "<p><strong>Link de recuperação (válido por 1 hora):</strong></p>";
        echo "<p><a href='$link' style='color: #007bff;'>$link</a></p>";
        echo "<hr>";
        echo "<p style='font-size: 13px; color: #555;'>Se você não solicitou isso, apenas ignore esta mensagem.</p>";
        echo "<p style='font-size: 13px;'>Atenciosamente,<br>Equipe EasyM</p>";

        echo "</div>";
    }

    // Mensagem padrão exibida sempre
    echo "<p style='text-align: center; font-family: Arial, sans-serif;'>Se este e-mail estiver cadastrado, você receberá o link de recuperação.</p>";
}
?>
