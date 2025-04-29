<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../formulario_login/form_login.html");
    exit;
}

require_once '../db.php';

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];

// Buscar valores totais
$stmtReceita = $pdo->prepare("SELECT SUM(valor) as total_receita FROM Entrada WHERE id_usuario = :usuario_id");
$stmtReceita->execute([':usuario_id' => $usuario_id]);
$receita = $stmtReceita->fetchColumn() ?? 0;

$stmtGastos = $pdo->prepare("SELECT SUM(valor_gasto) as total_gasto FROM Gasto WHERE usuario_id = :usuario_id");
$stmtGastos->execute([':usuario_id' => $usuario_id]);
$gastos = $stmtGastos->fetchColumn() ?? 0;

$saldo = $receita - $gastos;

// Buscar últimas entradas
$stmtEntradas = $pdo->prepare("SELECT descricao, valor FROM Entrada WHERE id_usuario = :usuario_id ORDER BY data_entrada DESC LIMIT 5");
$stmtEntradas->execute([':usuario_id' => $usuario_id]);
$entradas = $stmtEntradas->fetchAll(PDO::FETCH_ASSOC);

// Buscar últimos gastos
$stmtUltimosGastos = $pdo->prepare("SELECT nome_gasto, valor_gasto FROM Gasto WHERE usuario_id = :usuario_id ORDER BY data_gasto DESC LIMIT 5");
$stmtUltimosGastos->execute([':usuario_id' => $usuario_id]);
$ultimosGastos = $stmtUltimosGastos->fetchAll(PDO::FETCH_ASSOC);

// Buscar o nome e perfil do usuário
$nome = $_SESSION['usuario_nome'];

$stmtPerfil = $pdo->prepare("SELECT perfil FROM Usuario WHERE id = :id");
$stmtPerfil->execute([':id' => $usuario_id]);
$perfilUsuario = $stmtPerfil->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - EasyM</title>
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="container">
        <aside class="sidebar">
            <h2>📊 EasyM</h2>
            <nav>
                <ul>
                    <li><a href="dashboard.php">🏠 Painel</a></li>
                    <li><a href="../view/extrato_page/extrato_view.php">📄 Extrato</a></li>
                    <li><a href="../view/form_entrada/forms_entrada.html">➕ Nova Entrada</a></li>
                    <li><a href="../view/fomulario_gasto/forms_gasto.html">➖ Novo Gasto</a></li>
                    <li><a href="../view/formulário_perfil/forms_perfil.html">👤 Teste de Perfil</a></li>
                    <li><a href="../view/formulario_login/form_login.html">🚪 Logout</a></li>
                </ul>
            </nav>
        </aside>

        <main class="content">
            <header class="header">
                <h1>Bem-vindo, <?= htmlspecialchars($nome) ?>!</h1>
                <p style="font-size: 1.2em; color: #555;">
                    Perfil Financeiro: <strong><?= htmlspecialchars($perfilUsuario ?? 'Não definido') ?></strong>
                </p>
                <div class="profile-icon">👤</div>
            </header>

            <section class="cards">
                <div class="card">
                    <h3>Saldo Atual</h3>
                    <p>R$<?= number_format($saldo, 2, ',', '.') ?></p>
                </div>
                <div class="card">
                    <h3>Receita</h3>
                    <p>R$<?= number_format($receita, 2, ',', '.') ?></p>
                </div>
                <div class="card">
                    <h3>Gastos</h3>
                    <p>R$<?= number_format($gastos, 2, ',', '.') ?></p>
                </div>
            </section>

            <section class="charts">
                <canvas id="financeChart"></canvas>
            </section>

            <section class="tables">
                <div class="table-box">
                    <h3>Receitas</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Fonte</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($entradas) > 0): ?>
                                <?php foreach ($entradas as $entrada): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($entrada['descricao']) ?></td>
                                        <td>R$ <?= number_format($entrada['valor'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">Nenhuma receita encontrada</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-box">
                    <h3>Gastos</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($ultimosGastos) > 0): ?>
                                <?php foreach ($ultimosGastos as $gasto): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($gasto['nome_gasto']) ?></td>
                                        <td>R$ <?= number_format($gasto['valor_gasto'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2">Nenhum gasto encontrado</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('financeChart').getContext('2d');
        const financeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Saldo', 'Receita', 'Gastos'],
                datasets: [{
                    label: 'Visão Financeira Atual',
                    data: [<?= json_encode($saldo) ?>, <?= json_encode($receita) ?>, <?= json_encode($gastos) ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)', // Azul para saldo
                        'rgba(75, 192, 192, 0.6)', // Verde para receitas
                        'rgba(255, 99, 132, 0.6)'  // Vermelho para gastos
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>
