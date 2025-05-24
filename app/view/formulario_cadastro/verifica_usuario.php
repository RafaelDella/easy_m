<?php
// verifica_usuario.php

require_once '../../db.php'; // ajuste o caminho para o seu arquivo de conexão com o banco

header('Content-Type: application/json');

try {
    // Recebe o JSON enviado
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['erro' => 'Dados inválidos.']);
        exit;
    }

    $email = trim($input['email'] ?? '');
    $cpf = trim($input['cpf'] ?? '');
    $usuario = trim($input['usuario'] ?? '');

    if (empty($email) || empty($cpf) || empty($usuario)) {
        echo json_encode(['erro' => 'Campos obrigatórios não enviados.']);
        exit;
    }

    // Conectar ao banco
    $db = new PDO('mysql:host=localhost;dbname=easym', 'root', '1234'); // ajuste aqui

    // Prepara a verificação de cada campo
    $stmt = $db->prepare('SELECT email, cpf, usuario FROM Usuario WHERE email = :email OR cpf = :cpf OR usuario = :usuario');
    $stmt->execute([
        ':email' => $email,
        ':cpf' => $cpf,
        ':usuario' => $usuario
    ]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    // Inicializa resposta
    $resposta = [
        'emailExiste' => false,
        'cpfExiste' => false,
        'usuarioExiste' => false
    ];

    // Se encontrou algum registro, verifica o que bateu
    if ($resultado) {
        if ($resultado['email'] === $email) {
            $resposta['emailExiste'] = true;
        }
        if ($resultado['cpf'] === $cpf) {
            $resposta['cpfExiste'] = true;
        }
        if ($resultado['usuario'] === $usuario) {
            $resposta['usuarioExiste'] = true;
        }
    }

    echo json_encode($resposta);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro inesperado: ' . $e->getMessage()]);
}
?>
