<?php
// Iniciar sessão
session_start();

// Verificar autenticação
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

// Incluir a classe de conexão
require_once __DIR__ . '../../../../db.php';

// Conectar ao banco
$db = new DB();
$pdo = $db->connect();

// Obter o ID do usuário logado
$id_usuario = $_SESSION['id_usuario'];

// Buscar nome e perfil do usuário (para sidebar e header)
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Buscar total de Receitas
$stmtReceitasTotal = $pdo->prepare("SELECT SUM(valor) as total_receita FROM Entrada WHERE id_usuario = :id_usuario");
$stmtReceitasTotal->execute(['id_usuario' => $id_usuario]);
$totalReceita = $stmtReceitasTotal->fetchColumn() ?? 0;

// Buscar total de Gastos
$stmtGastosTotal = $pdo->prepare("SELECT SUM(valor_gasto) as total_gasto FROM Gasto WHERE id_usuario = :id_usuario");
$stmtGastosTotal->execute(['id_usuario' => $id_usuario]);
$totalGasto = $stmtGastosTotal->fetchColumn() ?? 0;

// Calcular saldo
$saldoAtual = $totalReceita - $totalGasto;

// Buscar últimas movimentações (receitas + gastos)
$stmtMovimentacoes = $pdo->prepare("
    (SELECT id_entrada AS id_transacao, descricao, valor, data_entrada AS data, 'Receita' AS tipo
    FROM Entrada
    WHERE id_usuario = :id_usuario)
    UNION ALL
    (SELECT id_gasto AS id_transacao, nome_gasto AS descricao, -valor_gasto AS valor, data_gasto AS data, 'Gasto' AS tipo
    FROM Gasto
    WHERE id_usuario = :id_usuario)
    ORDER BY data DESC
    LIMIT 10
");
$stmtMovimentacoes->execute(['id_usuario' => $id_usuario]);
$movimentacoes = $stmtMovimentacoes->fetchAll(PDO::FETCH_ASSOC);

// Buscar Receitas por mês
$stmtReceitasMes = $pdo->prepare("
    SELECT DATE_FORMAT(data_entrada, '%m/%y') AS mes, SUM(valor) AS total_receita
    FROM Entrada
    WHERE id_usuario = :id_usuario
    GROUP BY mes
    ORDER BY STR_TO_DATE(mes, '%m/%y')
");
$stmtReceitasMes->execute(['id_usuario' => $id_usuario]);
$receitasMes = $stmtReceitasMes->fetchAll(PDO::FETCH_KEY_PAIR);

// Buscar Gastos por mês
$stmtGastosMes = $pdo->prepare("
    SELECT DATE_FORMAT(data_gasto, '%m/%y') AS mes, SUM(valor_gasto) AS total_gasto
    FROM Gasto
    WHERE id_usuario = :id_usuario
    GROUP BY mes
    ORDER BY STR_TO_DATE(mes, '%m/%y')
");
$stmtGastosMes->execute(['id_usuario' => $id_usuario]);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/6-extrato.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>
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
            <a href="../dashboard/1-dashboard.php" class="botao-link">← Voltar para o Dashboard</a>
            <a href="../forms_entrada/1-forms_entrada.php" class="botao-link">➕ Adicionar Entrada</a>
            <a href="../forms_gasto/1-forms_gasto.php" class="botao-link">➖ Adicionar Gasto</a>
        </div>

        <h3>Movimentações Recentes</h3>
        <table class="tabela-extrato">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Tipo</th>
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
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nenhuma movimentação encontrada.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
        <main>


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
            <script src="../../../assets/js/components/sidebar.js"></script>



</body>

</html>