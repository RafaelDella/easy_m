<?php
session_start();
require_once '../../../app/db.php'; // Corrija o caminho conforme onde está o arquivo

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../../formulario_login/form_login.html');
    exit;
}

// Conectando ao banco
$db = new DB();
$pdo = $db->connect();

// Recebendo dados do formulário
$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$categoria = $_POST['categoria'];
$data_entrada = $_POST['data_entrada'];

// ID do usuário logado
$id_usuario = $_SESSION['usuario_id'];

try {
    // Preparando a query
    $stmt = $pdo->prepare("INSERT INTO Entrada (descricao, valor, categoria, data_entrada, id_usuario) VALUES (:descricao, :valor, :categoria, :data_entrada, :id_usuario)");

    // Executando
    $stmt->execute([
        ':descricao' => $descricao,
        ':valor' => $valor,
        ':categoria' => $categoria,
        ':data_entrada' => $data_entrada,
        ':id_usuario' => $id_usuario
    ]);

    // Sucesso
    echo "<script>
            alert('✅ Entrada registrada com sucesso!');
            window.location.href='forms_entrada.html'; // Redireciona para o formulário de entrada
        </script>";
} catch (PDOException $e) {
    echo "Erro ao registrar entrada: " . $e->getMessage();
}
