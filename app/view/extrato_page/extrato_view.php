<?php
session_start();
require_once '../../../app/db.php';

// Conectar ao banco
$db = new DB();
$pdo = $db->connect();

// ID do usuário logado
$usuario_id = $_SESSION['usuario_id'];

// Buscar total de Receitas
$stmtReceitasTotal = $pdo->prepare("SELECT SUM(valor) as total_receita FROM Entrada WHERE id_usuario = :usuario_id");
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
    WHERE id_usuario = :usuario_id)
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
        SELECT DATE_FORMAT(data_entrada, '%m/%y') AS mes, SUM(valor) AS total_receita
        FROM Entrada
        WHERE id_usuario = :usuario_id
        GROUP BY mes
        ORDER BY STR_TO_DATE(mes, '%m/%y')
    ");
$stmtReceitasMes->execute(['usuario_id' => $usuario_id]);
$receitasMes = $stmtReceitasMes->fetchAll(PDO::FETCH_KEY_PAIR);

// Buscar Gastos por mês para o gráfico
$stmtGastosMes = $pdo->prepare("
        SELECT DATE_FORMAT(data_gasto, '%m/%y') AS mes, SUM(valor_gasto) AS total_gasto
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


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Extrato EasyM</title>
    <link rel="stylesheet" href="../../assets/extrato_gasto/extrato.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="form-container">
        <h2>Extrato Financeiro</h2>

        <div class="saldo-container">
            <div class="card-financeiro" id="card-receita">
                <h3>Total de Receitas</h3>
                <p>R$ <?= number_format($totalReceita, 2, ',', '.') ?></p>
            </div>
            <div class="card-financeiro" id="card-gasto">
                <h3>Total de Gastos</h3>
                <p>R$ <?= number_format($totalGasto, 2, ',', '.') ?></p>
            </div>
            <div class="card-financeiro" id="card-saldo">
                <h3>Saldo Atual</h3>
                <p>R$ <?= number_format($saldoAtual, 2, ',', '.') ?></p>
            </div>
        </div>

        <div class="grafico-container">
            <canvas id="graficoFinanceiro"></canvas>
        </div>

        <div class="botoes-container">
            <a href="../dashboard.php" class="botao-link">← Voltar para o Dashboard</a>
            <a href="../form_entrada/forms_entrada.html" class="botao-link">➕ Adicionar Entrada</a>
            <a href="../fomulario_gasto/forms_gasto.html" class="botao-link">➖ Adicionar Gasto</a>
        </div>

        <h3>Movimentações Recentes</h3>
        <table class="tabela-extrato">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Tipo</th>
                    <th>Ações</th> <!-- NOVO -->
                </tr>
            </thead>
            <tbody>
                <?php if (count($movimentacoes) > 0): ?>
                    <?php foreach ($movimentacoes as $mov): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($mov['data'])) ?></td>
                            <td><?= htmlspecialchars($mov['descricao']) ?></td>
                            <td><?= ($mov['valor'] >= 0 ? '' : '-') . 'R$ ' . number_format(abs($mov['valor']), 2, ',', '.') ?></td>
                            <td><?= $mov['tipo'] ?></td>
                            <td>
                                <a href="../form_entrada/editar_entrada.php?id=<?= $mov['id_transacao'] ?>&tipo=<?= strtolower($mov['tipo']) ?>">✏️</a>
                                <form action="../form_entrada/excluir_entrada.php" method="POST" style="display:inline;" onsubmit="return confirm('Deseja realmente excluir?');">
                                    <input type="hidden" name="id" value="<?= $mov['id_transacao'] ?>">
                                    <input type="hidden" name="tipo" value="<?= strtolower($mov['tipo']) ?>">
                                    <button type="submit" style="background:none; border:none; cursor:pointer;">🗑️</button>
                                </form>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhuma movimentação encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>

    <script>
        const ctx = document.getElementById('graficoFinanceiro').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($meses) ?>,
                datasets: [{
                        label: 'Receitas',
                        data: <?= json_encode($dadosReceitas) ?>,
                        backgroundColor: '#4caf50'
                    },
                    {
                        label: 'Gastos',
                        data: <?= json_encode($dadosGastos) ?>,
                        backgroundColor: '#f44336'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#333',
                        bodyColor: '#333',
                        borderColor: '#ccc',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        stacked: false,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        grid: {
                            color: '#e0e0e0'
                        }
                    }
                }
            }
        });
    </script>



</body>

</html>