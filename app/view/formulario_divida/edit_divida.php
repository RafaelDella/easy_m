<?php
require_once '../../db.php';
$bd = new DB();
$conn = $bd->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("
    UPDATE Divida 
    SET nome_divida = ?, taxa_divida = ?, categoria_divida = ?, data_divida = ?, usuario_id = ?
    WHERE id_divida = ?
  ");
  $stmt->execute([
    $_POST['nome_divida'],
    $_POST['taxa_divida'],
    $_POST['categoria_divida'],
    $_POST['data_divida'],
    $_POST['usuario_id'],
    $_POST['id_divida']
  ]);
  header("Location: index.php?status=" . urlencode("Dívida atualizada."));
  exit;
}

$id = $_GET['id_divida'] ?? null;

if ($id) {
    $divida = $conn->query("SELECT * FROM Divida WHERE id_divida = 3")->fetch(PDO::FETCH_ASSOC);
} else {
    die("Erro: ID da dívida não fornecido.");
}

?>
<form method="POST" action="edit.php">
  <input type="hidden" name="id_divida" value="<?= $divida['id_divida'] ?>">
  <input type="text" name="nome_divida" value="<?= $divida['nome_divida'] ?>" required>
  <input type="number" name="taxa_divida" step="0.01" value="<?= $divida['taxa_divida'] ?>" required>
  <input type="text" name="categoria_divida" value="<?= $divida['categoria_divida'] ?>" required>
  <input type="date" name="data_divida" value="<?= $divida['data_divida'] ?>" required>
  <input type="number" name="usuario_id" value="<?= $divida['usuario_id'] ?>" required>
  <button type="submit">Salvar</button>
</form>
