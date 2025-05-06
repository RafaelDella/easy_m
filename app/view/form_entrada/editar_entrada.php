<?php
session_start();
require_once '../../../app/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../formulario_login/form_login.html");
    exit;
}

$db = new DB();
$pdo = $db->connect();
$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parte de ATUALIZAÇÃO
    $id = $_POST['id'];
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $categoria = $_POST['categoria'];
    $data = $_POST['data_entrada'];

    try {
        $stmt = $pdo->prepare("UPDATE Entrada 
            SET descricao = :descricao, valor = :valor, categoria = :categoria, data_entrada = :data 
            WHERE id_entrada = :id AND id_usuario = :usuario_id");

        $stmt->execute([
            'descricao' => $descricao,
            'valor' => $valor,
            'categoria' => $categoria,
            'data' => $data,
            'id' => $id,
            'usuario_id' => $usuario_id
        ]);

        echo "<script>
            alert('✅ Entrada atualizada com sucesso!');
            window.location.href='../extrato_page/extrato_view.php';
        </script>";
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar: " . $e->getMessage();
    }
} else {
    // Parte de EXIBIÇÃO (GET)
    if (!isset($_GET['id'])) {
        echo "ID não fornecido.";
        exit;
    }

    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM Entrada WHERE id_entrada = :id AND id_usuario = :usuario_id");
    $stmt->execute(['id' => $id, 'usuario_id' => $usuario_id]);
    $entrada = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entrada) {
        echo "⚠️ Entrada não encontrada.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Entrada</title>
    <link rel="stylesheet" href="../../assets/extrato_gasto/extrato.css">
</head>
<body>
    <div class="form-container">
        <h2>Editar Entrada</h2>
        <form action="editar_entrada.php" method="POST">
            <input type="hidden" name="id" value="<?= $entrada['id_entrada'] ?>">

            <label for="descricao">Descrição:</label>
            <input type="text" name="descricao" id="descricao" value="<?= htmlspecialchars($entrada['descricao']) ?>" required>

            <label for="valor">Valor:</label>
            <input type="number" name="valor" id="valor" step="0.01" value="<?= $entrada['valor'] ?>" required>

            <label for="categoria">Categoria:</label>
            <input type="text" name="categoria" id="categoria" value="<?= htmlspecialchars($entrada['categoria']) ?>">

            <label for="data_entrada">Data:</label>
            <input type="date" name="data_entrada" id="data_entrada" value="<?= $entrada['data_entrada'] ?>" required>

            <button type="submit">Salvar alterações</button>
            <a href="../extrato_page/extrato_view.php" class="botao-link">Cancelar</a>
        </form>
    </div>
</body>
</html>
