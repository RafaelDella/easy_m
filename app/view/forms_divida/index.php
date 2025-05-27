<?php
require_once '../../db.php';
$bd = new DB();
$conn = $bd->connect();
$dividas = $conn->query("
    SELECT d.*, u.nome AS nome_usuario 
    FROM Divida d 
    JOIN Usuario u ON d.usuario_id = u.id
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Gerenciador de Dívidas</title>
  <link rel="stylesheet" href="../../assets/forms_divida/forms_divida.css">
</head>
<body>
  <h1>Gerenciador de Dívidas</h1>
  <form action="create_divida.php" method="POST" onsubmit="return validateforms()">
    <input type="text" name="nome_divida" placeholder="Nome da dívida" required>
    <input type="number" name="taxa_divida" step="0.01" placeholder="Taxa (%)" required>
    <input type="text" name="categoria_divida" placeholder="Categoria" required>
    <input type="date" name="data_divida" required>
    <input type="number" name="usuario_id" placeholder="ID do usuário" required>
    <button type="submit">Adicionar</button>
  </form>
  <table>
    <tr>
      <th>ID</th><th>Nome</th><th>Taxa (%)</th><th>Categoria</th><th>Vencimento</th><th>Usuário</th><th>Ações</th>
    </tr>
    <?php foreach ($dividas as $d): ?>
      <tr>
        <td><?= $d['id_divida'] ?></td>
        <td><?= $d['nome_divida'] ?></td>
        <td><?= $d['taxa_divida'] ?></td>
        <td><?= $d['categoria_divida'] ?></td>
        <td><?= $d['data_divida'] ?></td>
        <td><?= $d['nome_usuario'] ?></td>
        <td>
          <a href="edit_divida.php?id=<?= $d['id_divida'] ?>">Editar</a> |
          <a href="delete_divida.php?id=<?= $d['id_divida'] ?>" onclick="return confirm('Confirmar exclusão?')">Excluir</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
  <div id="modal" class="modal">
    <div class="modal-content">
      <p id="modal-message"></p>
      <button onclick="closeModal()">Fechar</button>
    </div>
  </div>
  <script src="../../assets/forms_divida/forms_divida.js"></script>
  <?php if (isset($_GET['status'])): ?>
    <script>
      showModal("<?= htmlspecialchars($_GET['status']) ?>");
    </script>
  <?php endif; ?>
</body>
</html>
