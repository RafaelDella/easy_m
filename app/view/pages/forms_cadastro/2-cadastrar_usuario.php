<?php
// cadastrar_usuario.php

require_once '../../../db.php'; // Ajuste se necessário

header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../../../assets/src/PHPMailer.php';
require_once '../../../assets/src/SMTP.php';
require_once '../../../assets/src/Exception.php';

try {
    // Recebe o JSON enviado
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
        exit;
    }

    // Captura e limpa os dados recebidos
    $nome = trim($input['nome'] ?? '');
    $usuario = trim($input['usuario'] ?? '');
    $email = trim($input['email'] ?? '');
    $senha = $input['senha'] ?? '';
    $cpf = trim($input['cpf'] ?? '');
    $escolaridade = trim($input['escolaridade'] ?? '');
    $data_nascimento = trim($input['data_nascimento'] ?? '');
    $perfil = trim($input['perfil'] ?? 'usuario'); // Valor padrão 'usuario' se não for informado

    // Validações básicas
    if (empty($nome) || empty($usuario) || empty($email) || empty($senha) || empty($cpf) || empty($escolaridade) || empty($data_nascimento)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail inválido.']);
        exit;
    }

    if (strlen($senha) < 8) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A senha deve ter no mínimo 8 caracteres.']);
        exit;
    }

    // Conexão com o banco de dados
    $db = new PDO('mysql:host=localhost;dbname=easym', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verifica se email, CPF ou usuário já existem
    $stmtVerificar = $db->prepare('SELECT id_usuario FROM Usuario WHERE email = :email OR cpf = :cpf OR usuario = :usuario');
    $stmtVerificar->execute([
        ':email' => $email,
        ':cpf' => $cpf,
        ':usuario' => $usuario
    ]);

    if ($stmtVerificar->rowCount() > 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário, e-mail ou CPF já cadastrados.']);
        exit;
    }

    // Inserção do novo usuário
    $stmt = $db->prepare('INSERT INTO Usuario (nome, usuario, email, senha, cpf, escolaridade, perfil, data_nascimento) 
                          VALUES (:nome, :usuario, :email, :senha, :cpf, :escolaridade, :perfil, :data_nascimento)');

    $stmt->execute([
        ':nome' => $nome,
        ':usuario' => $usuario,
        ':email' => $email,
        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
        ':cpf' => $cpf,
        ':escolaridade' => $escolaridade,
        ':perfil' => $perfil,
        ':data_nascimento' => $data_nascimento
    ]);

    // Envio do e-mail de boas-vindas
    try {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'brunokioshi01@gmail.com';
        $mail->Password = 'bfjm joof axdq oifm'; // 🔒 Senha de app
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('brunokioshi01@gmail.com', 'EasyM - Suporte');
        $mail->addAddress($email, $nome);
        $mail->isHTML(true);
        $mail->Subject = 'Confirmação de Cadastro - EasyM';
        $mail->Body = "
        <h2>Cadastro realizado com sucesso!</h2>
        <p>Olá <strong>$nome</strong>,</p>
        <p>Seu cadastro no EasyM foi concluído com sucesso.</p>
        <p>Agora você pode acessar o sistema usando seu login e senha:</p>
        <p><a href='http://http://localhost/easy_m/app/view/pages/forms_login/1-forms_login.html'>Acessar o EasyM</a></p>
        <hr>
        <p style='font-size:12px'>Se você não realizou esse cadastro, ignore este e-mail.</p>
    ";

        $mail->send();
        // (opcional) log ou mensagem de sucesso do envio
    } catch (Exception $e) {
        // (opcional) registrar erro mas não interromper o fluxo
        // error_log("Erro ao enviar e-mail: {$mail->ErrorInfo}");
    }


    echo json_encode(['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso.']);
} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro inesperado: ' . $e->getMessage()]);
}
