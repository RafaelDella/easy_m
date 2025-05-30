<?php
session_start();

// Verifica se o usuário está logado
// 'id_usuario' é usado para consistência com os outros arquivos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

require_once __DIR__ . '../../../../db.php'; // Caminho ajustado para consistência

// Conexão com o banco de dados
$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario']; // Alterado para 'id_usuario' para consistência

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e valida os dados do formulário
    $id = $_POST['id'] ?? null; // ID da entrada a ser atualizada
    $descricao = trim($_POST['descricao'] ?? '');
    $valor = filter_var($_POST['valor'] ?? 0, FILTER_VALIDATE_FLOAT); // Valida como float
    $categoria = $_POST['categoria'] ?? '';
    $data = $_POST['data_entrada'] ?? '';

    // Validação dos campos obrigatórios e do ID
    if (empty($id) || $valor === false || $valor <= 0 || empty($descricao) || empty($categoria) || empty($data)) {
        echo "<script>alert('❌ Por favor, preencha todos os campos corretamente para atualizar a entrada.'); window.location.href='1-forms_entrada.php';</script>";
        exit;
    }

    try {
        // Atualiza os dados da entrada no banco
        $sql = "UPDATE Entrada SET descricao = :descricao, valor = :valor, categoria = :categoria, data_entrada = :data 
                 WHERE id_entrada = :id AND id_usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':descricao' => $descricao,
            ':valor' => $valor,
            ':categoria' => $categoria,
            ':data' => $data,
            ':id' => $id,
            ':id_usuario' => $id_usuario
        ]);

        echo "<script>
            alert('✅ Entrada atualizada com sucesso!');
            window.location.href = '1-forms_entrada.php'; // Redireciona para a página de formulário de entrada
        </script>";
        exit;
    } catch (PDOException $e) {
        // Melhor tratamento de erro para o usuário final
        echo "<script>
            alert('❌ Erro ao atualizar entrada: Por favor, tente novamente mais tarde.');
            window.location.href='1-forms_entrada.php'; // Redireciona de volta para a página de formulário
        </script>";
        // Em um ambiente de produção, você registraria $e->getMessage() em um arquivo de log
        // error_log("Erro ao atualizar entrada: " . $e->getMessage());
        exit;
    }
} else {
    // Caso a requisição não seja POST, redireciona ou mostra uma mensagem de erro
    echo "<script>alert('Requisição inválida.'); window.location.href='1-forms_entrada.php';</script>";
    exit;
}
?>