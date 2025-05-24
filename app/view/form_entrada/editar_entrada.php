<?php
session_start();

$_SESSION['usuario_id'] = 5; 

require_once '../../../app/db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../formulario_login/form_login.html");
    exit;
}

// Conexão com o banco de dados
$db = new DB();
$pdo = $db->connect();
$usuario_id = $_SESSION['usuario_id'];

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta os dados do formulário
    $id = $_POST['id'];
    $descricao = trim($_POST['descricao']);
    $valor = $_POST['valor'];
    $categoria = $_POST['categoria'];
    $data = $_POST['data_entrada'];

    // Validação simples dos campos obrigatórios
    if (empty($descricao) || empty($valor) || empty($categoria) || empty($data)) {
        echo "<script>alert('❌ Todos os campos são obrigatórios.'); window.history.back();</script>";
        exit;
    }

    try {
        // Atualiza os dados da entrada no banco
        $sql = "UPDATE Entrada SET descricao = :descricao, valor = :valor, categoria = :categoria, data_entrada = :data 
                WHERE id_entrada = :id AND id_usuario = :usuario_id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':descricao' => $descricao,
            ':valor' => $valor,
            ':categoria' => $categoria,
            ':data' => $data,
            ':id' => $id,
            ':usuario_id' => $usuario_id
        ]);

        echo "<script>
            alert('✅ Entrada atualizada com sucesso!');
            window.location.href = '../extrato_page/extrato_view.php';
        </script>";
    } catch (PDOException $e) {
        echo "Erro ao atualizar entrada: " . $e->getMessage();
    }
}
?>
