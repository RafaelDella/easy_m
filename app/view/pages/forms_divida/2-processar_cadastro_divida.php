<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

require_once __DIR__ . '/../../../db.php';

$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario'];

// Coleta e validação dos dados do formulário
$nome_divida     = trim($_POST['nome_divida'] ?? '');
$valor_total     = filter_var($_POST['valor_total'] ?? 0, FILTER_VALIDATE_FLOAT);
$valor_pago      = filter_var($_POST['valor_pago'] ?? 0, FILTER_VALIDATE_FLOAT);
$taxa_divida     = filter_var($_POST['taxa_divida'] ?? 0, FILTER_VALIDATE_FLOAT);
$categoria       = $_POST['categoria_divida'] ?? '';
$data_vencimento = $_POST['data_vencimento'] ?? '';

// Verificações básicas
if (
    empty($nome_divida) ||
    $valor_total === false || $valor_total <= 0 ||
    $valor_pago === false || $valor_pago < 0 ||
    $taxa_divida === false || $taxa_divida < 0 ||
    empty($categoria) ||
    empty($data_vencimento)
) {
    echo "<script>
        alert('❌ Preencha todos os campos corretamente para cadastrar a dívida.');
        window.location.href='1-forms_divida.php';
    </script>";
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO Divida (nome_divida, valor_total, valor_pago, taxa_divida, categoria_divida, data_vencimento, id_usuario)
        VALUES (:nome, :total, :pago, :taxa, :categoria, :vencimento, :id_usuario)
    ");

    $stmt->execute([
        ':nome'        => $nome_divida,
        ':total'       => $valor_total,
        ':pago'        => $valor_pago,
        ':taxa'        => $taxa_divida,
        ':categoria'   => $categoria,
        ':vencimento'  => $data_vencimento,
        ':id_usuario'  => $id_usuario
    ]);

    echo "<script>
        alert('✅ Dívida cadastrada com sucesso!');
        window.location.href='1-forms_divida.php';
    </script>";
    exit;
} catch (PDOException $e) {
    echo "<script>
        alert('❌ Erro ao cadastrar dívida: Por favor, tente novamente mais tarde.');
        window.location.href='1-forms_divida.php';
    </script>";
    // error_log("Erro ao cadastrar dívida: " . $e->getMessage());
    exit;
}
