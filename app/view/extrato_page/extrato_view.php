<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Extrato EasyM</title>
    <link rel="stylesheet" href="assets/assets/extrato_gasto/extrato.css">
</head>
<body>
    <div class="container">
        <h1>Extrato de Saídas</h1>

        <?php
        require_once '../../db.php';

        $usuario_id = 1;

        $stmtSaidas = $pdo->prepare("SELECT id_gasto AS id_transacao, nome_gasto AS descricao, valor_gasto AS valor, data_gasto AS data FROM Gasto WHERE usuario_id = :usuario_id ORDER BY data_gasto DESC");
        $stmtSaidas->execute(['usuario_id' => $usuario_id]);
        $saidas = $stmtSaidas->fetchAll();

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
        $entradas = $stmtEntradas->fetchAll();

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

    </div>
</body>
</html>
