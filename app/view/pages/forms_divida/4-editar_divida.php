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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nome_divida = trim($_POST['nome_divida'] ?? '');
    $valor_total = filter_var($_POST['valor_total'] ?? 0, FILTER_VALIDATE_FLOAT);
    $valor_pago = filter_var($_POST['valor_pago'] ?? 0, FILTER_VALIDATE_FLOAT);
    $taxa = filter_var($_POST['taxa_divida'] ?? 0, FILTER_VALIDATE_FLOAT);
    $categoria = $_POST['categoria_divida'] ?? '';
    $data_vencimento = $_POST['data_vencimento'] ?? '';

    if (
        empty($id) || empty($nome_divida) ||
        $valor_total === false || $valor_total <= 0 ||
        $valor_pago === false || $valor_pago < 0 ||
        $taxa === false || $taxa < 0 ||
        empty($categoria) || empty($data_vencimento)
    ) {
        echo "<script>
            alert('❌ Por favor, preencha todos os campos corretamente para atualizar a dívida.');
            window.location.href='1-forms_divida.php';
        </script>";
        exit;
    }

    try {
        $sql = "UPDATE Divida 
                SET nome_divida = :nome, valor_total = :total, valor_pago = :pago, 
                    taxa_divida = :taxa, categoria_divida = :categoria, data_vencimento = :vencimento
                WHERE id_divida = :id AND id_usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome_divida,
            ':total' => $valor_total,
            ':pago' => $valor_pago,
            ':taxa' => $taxa,
            ':categoria' => $categoria,
            ':vencimento' => $data_vencimento,
            ':id' => $id,
            ':id_usuario' => $id_usuario
        ]);

        echo "<script>
            alert('✅ Dívida atualizada com sucesso!');
            window.location.href = '1-forms_divida.php';
        </script>";
        exit;
    } catch (PDOException $e) {
        echo "<script>
            alert('❌ Erro ao atualizar dívida: Por favor, tente novamente mais tarde.');
            window.location.href='1-forms_divida.php';
        </script>";
        // error_log("Erro ao atualizar dívida: " . $e->getMessage());
        exit;
    }
} else {
    echo "<script>alert('Requisição inválida.'); window.location.href='1-forms_divida.php';</script>";
    exit;
}
