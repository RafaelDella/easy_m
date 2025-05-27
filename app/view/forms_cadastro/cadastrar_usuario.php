<?php
// cadastrar_usuario.php

require_once '../../db.php'; // Ajuste aqui para o seu arquivo de conexão

header('Content-Type: application/json');

try {
    // Recebe o JSON enviado
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
        exit;
    }

    $nome = trim($input['nome'] ?? '');
    $usuario = trim($input['usuario'] ?? '');
    $email = trim($input['email'] ?? '');
    $senha = $input['senha'] ?? '';
    $cpf = trim($input['cpf'] ?? '');
    $escolaridade = trim($input['escolaridade'] ?? '');
    $data_nascimento = trim($input['data_nascimento'] ?? '');

    // Validações básicas no servidor também
    if (empty($nome) || empty($usuario) || empty($email) || empty($senha) || empty($cpf) || empty($escolaridade) || empty($data_nascimento)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Todos os campos são obrigatórios.']);
        exit;
    }

    if (strlen($senha) < 8) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A senha deve ter no mínimo 8 caracteres.']);
        exit;
    }

    // Conectar ao banco
    $db = new PDO('mysql:host=localhost;dbname=easym', 'root', ''); // ajuste aqui

    // Verifica se email, cpf ou usuario já existem - segurança adicional
    $stmtVerificar = $db->prepare('SELECT id FROM Usuario WHERE email = :email OR cpf = :cpf OR usuario = :usuario');
    $stmtVerificar->execute([
        ':email' => $email,
        ':cpf' => $cpf,
        ':usuario' => $usuario
    ]);

    if ($stmtVerificar->rowCount() > 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário, e-mail ou CPF já cadastrados.']);
        exit;
    }

    // Inserir novo usuário
    $stmt = $db->prepare('INSERT INTO Usuario (nome, usuario, email, senha, cpf, escolaridade, data_nascimento) 
                        VALUES (:nome, :usuario, :email, :senha, :cpf, :escolaridade, :data_nascimento)');

    $stmt->execute([
        ':nome' => $nome,
        ':usuario' => $usuario,
        ':email' => $email,
        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
        ':cpf' => $cpf,
        ':escolaridade' => $escolaridade,
        ':data_nascimento' => $data_nascimento
    ]);

    echo json_encode(['sucesso' => true]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no banco de dados: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro inesperado: ' . $e->getMessage()]);
}
?>
