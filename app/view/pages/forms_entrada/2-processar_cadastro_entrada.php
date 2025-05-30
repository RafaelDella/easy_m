<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
} 

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario'];

// Validação básica dos dados POST
$descricao = $_POST['descricao'] ?? '';
$valor = filter_var($_POST['valor'] ?? 0, FILTER_VALIDATE_FLOAT);
$categoria = $_POST['categoria'] ?? '';
$data_entrada = $_POST['data_entrada'] ?? '';

// Verificar se os dados obrigatórios estão preenchidos e são válidos
if (empty($descricao) || $valor === false || $valor <= 0 || empty($categoria) || empty($data_entrada)) {
    echo "<script>
        alert('❌ Por favor, preencha todos os campos corretamente para cadastrar a entrada.');
        window.location.href='1-forms_entrada.php'; // Redireciona de volta para a página de formulário
    </script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO Entrada (descricao, valor, categoria, data_entrada, id_usuario)
                           VALUES (:descricao, :valor, :categoria, :data_entrada, :id_usuario)");

    $stmt->execute([
        'descricao' => $descricao,
        'valor' => $valor,
        'categoria' => $categoria,
        'data_entrada' => $data_entrada,
        'id_usuario' => $id_usuario
    ]);

    echo "<script>
        alert('✅ Entrada registrada com sucesso!');
        window.location.href='1-forms_entrada.php'; // Redireciona para a mesma página, onde a lista será atualizada
    </script>";
    exit;
} catch (PDOException $e) {
    // Em um ambiente de produção, logar o erro e mostrar uma mensagem genérica.
    // Para desenvolvimento, pode-se mostrar o erro.
    echo "Erro ao registrar entrada: " . $e->getMessage();
    // Você pode redirecionar para uma página de erro ou de volta ao formulário
    // header("Location: formulario_entrada.php?erro=db_error");
    // exit;
}
?>