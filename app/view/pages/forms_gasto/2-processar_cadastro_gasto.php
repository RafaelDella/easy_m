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
$nome_gasto = $_POST['nome_gasto'] ?? '';
$desc_gasto = $_POST['desc_gasto'] ?? '';
$valor_gasto = filter_var($_POST['valor_gasto'] ?? 0, FILTER_VALIDATE_FLOAT);
$categoria_gasto = $_POST['categoria_gasto'] ?? '';
$is_imprevisto = isset($_POST['is_imprevisto']) ? 1 : 0; // 1 se marcado, 0 se não
$data_gasto = $_POST['data_gasto'] ?? '';

// Verificar se os dados obrigatórios estão preenchidos e são válidos
if (empty($nome_gasto) || empty($desc_gasto) || $valor_gasto === false || $valor_gasto <= 0 || empty($categoria_gasto) || empty($data_gasto)) {
    echo "<script>
        alert('❌ Por favor, preencha todos os campos corretamente para cadastrar o gasto.');
        window.location.href='1-forms_gasto.php'; // Redireciona de volta para a página de formulário
    </script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO Gasto (nome_gasto, desc_gasto, categoria_gasto, valor_gasto, is_imprevisto, data_gasto, id_usuario)
                           VALUES (:nome_gasto, :desc_gasto, :categoria_gasto, :valor_gasto, :is_imprevisto, :data_gasto, :id_usuario)");

    $stmt->execute([
        'nome_gasto' => $nome_gasto,
        'desc_gasto' => $desc_gasto,
        'categoria_gasto' => $categoria_gasto,
        'valor_gasto' => $valor_gasto,
        'is_imprevisto' => $is_imprevisto,
        'data_gasto' => $data_gasto,
        'id_usuario' => $id_usuario
    ]);

    echo "<script>
        alert('✅ Gasto registrado com sucesso!');
        window.location.href='1-forms_gasto.php'; // Redireciona para a mesma página, onde a lista será atualizada
    </script>";
    exit;
} catch (PDOException $e) {
    echo "Erro ao registrar gasto: " . $e->getMessage();
    // Você pode redirecionar para uma página de erro ou de volta ao formulário
    // header("Location: 1-forms_gasto.php?erro=db_error");
    // exit;
}
?> 