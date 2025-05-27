<<<<<<< Updated upstream
=======
<?php
session_start();
require_once '../../../app/db.php';

// Conectar ao banco
$db = new DB();
$pdo = $db->connect();

// ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Buscar total de Receitas
$stmtReceitasTotal = $pdo->prepare("SELECT SUM(valor) as total_receita FROM Entrada WHERE usuario_id = :usuario_id");
$stmtReceitasTotal->execute(['usuario_id' => $usuario_id]);
$totalReceita = $stmtReceitasTotal->fetchColumn() ?? 0;

// Buscar total de Gastos
$stmtGastosTotal = $pdo->prepare("SELECT SUM(valor_gasto) as total_gasto FROM Gasto WHERE usuario_id = :usuario_id");
$stmtGastosTotal->execute(['usuario_id' => $usuario_id]);
$totalGasto = $stmtGastosTotal->fetchColumn() ?? 0;

// Calcular saldo
$saldoAtual = $totalReceita - $totalGasto;

// Buscar últimas movimentações
$stmtMovimentacoes = $pdo->prepare("
    (SELECT id_entrada AS id_transacao, descricao, valor, data_entrada AS data, 'Receita' AS tipo
    FROM Entrada
    WHERE usuario_id = :usuario_id)
    UNION ALL
    (SELECT id_gasto AS id_transacao, nome_gasto AS descricao, -valor_gasto AS valor, data_gasto AS data, 'Gasto' AS tipo
    FROM Gasto
    WHERE usuario_id = :usuario_id)
    ORDER BY data DESC
    LIMIT 10
");

$stmtMovimentacoes->execute(['usuario_id' => $usuario_id]);
$movimentacoes = $stmtMovimentacoes->fetchAll(PDO::FETCH_ASSOC);

// Buscar Receitas por mês para o gráfico
$stmtReceitasMes = $pdo->prepare("
        SELECT DATE_formularioAT(data_entrada, '%m/%y') AS mes, SUM(valor) AS total_receita
        FROM Entrada
        WHERE usuario_id = :usuario_id
        GROUP BY mes
        ORDER BY STR_TO_DATE(mes, '%m/%y')
    ");
$stmtReceitasMes->execute(['usuario_id' => $usuario_id]);
$receitasMes = $stmtReceitasMes->fetchAll(PDO::FETCH_KEY_PAIR);

// Buscar Gastos por mês para o gráfico
$stmtGastosMes = $pdo->prepare("
        SELECT DATE_formularioAT(data_gasto, '%m/%y') AS mes, SUM(valor_gasto) AS total_gasto
        FROM Gasto
        WHERE usuario_id = :usuario_id
        GROUP BY mes
        ORDER BY STR_TO_DATE(mes, '%m/%y')
    ");
$stmtGastosMes->execute(['usuario_id' => $usuario_id]);
$gastosMes = $stmtGastosMes->fetchAll(PDO::FETCH_KEY_PAIR);

// Unificar meses
$meses = array_unique(array_merge(array_keys($receitasMes), array_keys($gastosMes)));
sort($meses);

// Preparar arrays para o gráfico
$dadosReceitas = [];
$dadosGastos = [];

foreach ($meses as $mes) {
    $dadosReceitas[] = $receitasMes[$mes] ?? 0;
    $dadosGastos[] = $gastosMes[$mes] ?? 0;
}
?>


>>>>>>> Stashed changes
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Extrato EasyM</title>
    <link rel="stylesheet" href="assets/assets/extrato_gasto/extrato.css">
</head>
<body>
<<<<<<< Updated upstream
    <div class="container">
        <h1>Extrato de Saídas</h1>

        <?php
        require_once '../../db.php';
=======
    <div class="formulario-container">
        <h2>Extrato Financeiro</h2>

        <div class="saldo-container">
            <div class="card-financeiro" id="card-receita">
                <h3>Total de Receitas</h3>
                <p>R$ <?= number_formularioat($totalReceita, 2, ',', '.') ?></p>
            </div>
            <div class="card-financeiro" id="card-gasto">
                <h3>Total de Gastos</h3>
                <p>R$ <?= number_formularioat($totalGasto, 2, ',', '.') ?></p>
            </div>
            <div class="card-financeiro" id="card-saldo">
                <h3>Saldo Atual</h3>
                <p>R$ <?= number_formularioat($saldoAtual, 2, ',', '.') ?></p>
            </div>
        </div>
>>>>>>> Stashed changes

        $usuario_id = 1;

<<<<<<< Updated upstream
        $stmtSaidas = $pdo->prepare("SELECT id_gasto AS id_transacao, nome_gasto AS descricao, valor_gasto AS valor, data_gasto AS data FROM Gasto WHERE usuario_id = :usuario_id ORDER BY data_gasto DESC");
        $stmtSaidas->execute(['usuario_id' => $usuario_id]);
        $saidas = $stmtSaidas->fetchAll();
=======
        <div class="botoes-container">
            <a href="../dashboard.php" class="botao-link">← Voltar para o Dashboard</a>
<<<<<<< Updated upstream
            <a href="../formulario_entrada/formularios_entrada.html" class="botao-link">➕ Adicionar Entrada</a>
            <a href="../formularioulario_gasto/formularios_gasto.html" class="botao-link">➖ Adicionar Gasto</a>
=======
            <a href="../form_entrada/forms_entrada.html" class="botao-link">➕ Adicionar Entrada</a>
            <a href="../forms_gasto/forms_gasto.html" class="botao-link">➖ Adicionar Gasto</a>
>>>>>>> Stashed changes
        </div>
>>>>>>> Stashed changes

        if (count($saidas) == 0) {
            echo "<p>⚠️ Nenhuma saída registrada.</p>";
        } else {
            echo "<table>
                    <thead>
                        <tr>
<<<<<<< Updated upstream
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Valor (R$)</th>
                            <th>Data</th>
=======
                            <td><?= date('d/m/Y', strtotime($mov['data'])) ?></td>
                            <td><?= htmlspecialchars($mov['descricao']) ?></td>
                            <td><?= ($mov['valor'] >= 0 ? '' : '-') . 'R$ ' . number_formularioat(abs($mov['valor']), 2, ',', '.') ?></td>
                            <td><?= $mov['tipo'] ?></td>
                            <td>
                                <?php if (strtolower($mov['tipo']) === 'receita'): ?>
                                    <a href="../formulario_entrada/formularios_entrada.html?id=<?= $mov['id_transacao'] ?>&tipo=receita">✏️</a>
                                    <formulario action="../formulario_entrada/excluir_entrada.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir esta entrada?');">
                                    <?php else: ?>
<<<<<<< Updated upstream
                                        <a href="../formularioulario_gasto/formularios_gasto.html?id=<?= $mov['id_transacao'] ?>&tipo=gasto">✏️</a>
                                        <formulario action="../formularioulario_gasto/excluir_gasto.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir este gasto?');">
=======
                                        <a href="../forms_gasto/forms_gasto.html?id=<?= $mov['id_transacao'] ?>&tipo=gasto">✏️</a>
                                        <form action="../forms_gasto/excluir_gasto.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir este gasto?');">
>>>>>>> Stashed changes
                                        <?php endif; ?>
                                        <input type="hidden" name="id" value="<?= $mov['id_transacao'] ?>">
                                        <input type="hidden" name="tipo" value="<?= strtolower($mov['tipo']) ?>">
                                        <button type="submit" style="background:none; border:none; cursor:pointer;">🗑️</button>
                                        </formulario>
                            </td>
>>>>>>> Stashed changes
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
        $stmtEntradas = $pdo->prepare("SELECT id_entrada AS id_deposito, descricao, valor, data_entrada AS data FROM Entrada WHERE usuario_id = :usuario_id ORDER BY data_entrada DESC");
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
