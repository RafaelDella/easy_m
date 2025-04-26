<?php
session_start();
require_once '../../../app/db.php';

// Conectar ao banco
$db = new DB();
$pdo = $db->connect();

// ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Extrato EasyM</title>
    <link rel="stylesheet" href="../../assets/extrato_gasto/extrato.css">
</head>

<body>
    <div class="container">
        <h1>Extrato de Saídas</h1>

        <?php
        // Consulta Extrato de Saídas
        $stmtSaidas = $pdo->prepare("SELECT id_gasto AS id_transacao, nome_gasto AS descricao, valor_gasto AS valor, data_gasto AS data FROM Gasto WHERE usuario_id = :usuario_id ORDER BY data_gasto DESC");
        $stmtSaidas->execute(['usuario_id' => $usuario_id]);
        $saidas = $stmtSaidas->fetchAll(PDO::FETCH_ASSOC);

        if (count($saidas) == 0) {
            echo "<p>⚠️ Nenhuma saída registrada.</p>";
        } else {
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Valor (R$)</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($saidas as $saida) {
                echo "<tr>
                        <td>{$saida['id_transacao']}</td>
                        <td>{$saida['descricao']}</td>
                        <td>R$ " . number_format($saida['valor'], 2, ',', '.') . "</td>
                        <td>" . date('d/m/Y', strtotime($saida['data'])) . "</td>
                    </tr>";
            }
            echo "</tbody></table>";
        }
        ?>

        <h1>Extrato de Entradas</h1>

        <?php
        // Consulta Extrato de Entradas
        $stmtEntradas = $pdo->prepare("SELECT id_entrada AS id_deposito, descricao, valor, data_entrada AS data FROM Entrada WHERE id_usuario = :usuario_id ORDER BY data_entrada DESC");
        $stmtEntradas->execute(['usuario_id' => $usuario_id]);
        $entradas = $stmtEntradas->fetchAll(PDO::FETCH_ASSOC);

        if (count($entradas) == 0) {
            echo "<p>⚠️ Nenhuma entrada registrada.</p>";
        } else {
            echo "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Valor (R$)</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($entradas as $entrada) {
                echo "<tr>
                        <td>{$entrada['id_deposito']}</td>
                        <td>{$entrada['descricao']}</td>
                        <td>R$ " . number_format($entrada['valor'], 2, ',', '.') . "</td>
                        <td>" . date('d/m/Y', strtotime($entrada['data'])) . "</td>
                    </tr>";
            }
            echo "</tbody></table>";
        }
        ?>

        <hr style="margin: 30px 0;">

        <h2>➕ Nova Entrada</h2>

        <form action="../form_entrada/salvar_entrada.php" method="POST" style="margin-top: 20px;">
            <label for="descricao">Descrição:</label><br>
            <input type="text" id="descricao" name="descricao" required><br><br>

            <label for="valor">Valor:</label><br>
            <input type="number" id="valor" name="valor" step="0.01" required><br><br>

            <label for="categoria">Categoria:</label><br>
            <select id="categoria" name="categoria" required>
                <option value="">Selecione</option>
                <option value="Salário">Salário</option>
                <option value="Freelance">Freelance</option>
                <option value="Presente">Presente</option>
                <option value="Outro">Outro</option>
            </select><br><br>

            <label for="data_entrada">Data:</label><br>
            <input type="date" id="data_entrada" name="data_entrada" required><br><br>

            <button type="submit">Salvar Entrada</button>
        </form>

        <hr style="margin: 30px 0;">
        <h2>➖ Novo Gasto</h2>

        <form action="../fomulario_gasto/processa_gasto.php" method="POST" style="margin-top: 20px;">
            <label for="nome_gasto">Nome do Gasto:</label><br>
            <input type="text" id="nome_gasto" name="nome_gasto" maxlength="50" required><br><br>

            <label for="desc_gasto">Descrição:</label><br>
            <textarea id="desc_gasto" name="desc_gasto" maxlength="150" rows="3"></textarea><br><br>

            <label for="categoria_gasto">Categoria:</label><br>
            <select id="categoria_gasto" name="categoria_gasto" required>
                <option value="">Selecione...</option>
                <option value="Alimentação">Alimentação</option>
                <option value="Transporte">Transporte</option>
                <option value="Educação">Educação</option>
                <option value="Lazer">Lazer</option>
                <option value="Moradia">Moradia</option>
                <option value="Outros">Outros</option>
            </select><br><br>

            <label for="valor_gasto">Valor (R$):</label><br>
            <input type="number" id="valor_gasto" name="valor_gasto" step="0.01" required><br><br>

            <label for="data_gasto">Data do Gasto:</label><br>
            <input type="date" id="data_gasto" name="data_gasto" required><br><br>

            <input type="checkbox" id="is_imprevisto" name="is_imprevisto">
            <label for="is_imprevisto">É um imprevisto?</label><br><br>

            <button type="submit">Salvar Gasto</button>
        </form>


    </div>
</body>

</html>