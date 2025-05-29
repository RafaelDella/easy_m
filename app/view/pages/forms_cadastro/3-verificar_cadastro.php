<?php
// verifica_usuario.php

require_once '../../../db.php'; // ajuste o caminho conforme sua estrutura

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    echo json_encode(['erro' => 'Método não permitido. Use POST.']);
    exit;
}

try {
    // Recebe o JSON enviado
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['erro' => 'Dados inválidos.']);
        exit;
    }

    // Limpa e valida os dados
    $email = trim($input['email'] ?? '');
    $cpf = trim($input['cpf'] ?? '');
    $usuario = trim($input['usuario'] ?? '');

    if (empty($email) && empty($cpf) && empty($usuario)) {
        echo json_encode(['erro' => 'Pelo menos um campo (email, cpf ou usuario) deve ser enviado.']);
        exit;
    }

    // Conecta ao banco
    $db = new PDO('mysql:host=localhost;dbname=easym', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta usando os campos disponíveis
    $sql = 'SELECT email, cpf, usuario FROM Usuario WHERE ';
    $conditions = [];
    $params = [];

    if (!empty($email)) {
        $conditions[] = 'email = :email';
        $params[':email'] = $email;
    }

    if (!empty($cpf)) {
        $conditions[] = 'cpf = :cpf';
        $params[':cpf'] = $cpf;
    }

    if (!empty($usuario)) {
        $conditions[] = 'usuario = :usuario';
        $params[':usuario'] = $usuario;
    }

    $sql .= implode(' OR ', $conditions);

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inicializa resposta
    $resposta = [
        'emailExiste' => false,
        'cpfExiste' => false,
        'usuarioExiste' => false
    ];

    foreach ($resultado as $linha) {
        if (!empty($email) && $linha['email'] === $email) {
            $resposta['emailExiste'] = true;
        }
        if (!empty($cpf) && $linha['cpf'] === $cpf) {
            $resposta['cpfExiste'] = true;
        }
        if (!empty($usuario) && $linha['usuario'] === $usuario) {
            $resposta['usuarioExiste'] = true;
        }
    }

    echo json_encode($resposta);

} catch (PDOException $e) {
    echo json_encode(['erro' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['erro' => 'Erro inesperado: ' . $e->getMessage()]);
}
