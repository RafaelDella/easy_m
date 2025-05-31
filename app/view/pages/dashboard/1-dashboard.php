<?php
// public/1-dashboard.php

session_start();

// 1. Verificação de autenticação
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../forms_login/1-forms_login.html");
    exit;
}

// 2. Conexão com o banco de dados
require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

$id_usuario = $_SESSION['id_usuario'];

// 3. Obtenção de dados do usuário
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// 4. Obtenção de dados específicos do Dashboard
$stmtReceita = $pdo->prepare("SELECT SUM(valor) as total_receita FROM Entrada WHERE id_usuario = :id_usuario");
$stmtReceita->execute([':id_usuario' => $id_usuario]);
$receita = $stmtReceita->fetchColumn() ?? 0;

$stmtGastos = $pdo->prepare("SELECT SUM(valor_gasto) as total_gasto FROM Gasto WHERE id_usuario = :id_usuario");
$stmtGastos->execute([':id_usuario' => $id_usuario]);
$gastos = $stmtGastos->fetchColumn() ?? 0;

$saldo = $receita - $gastos;

$stmtEntradas = $pdo->prepare("SELECT descricao, valor FROM Entrada WHERE id_usuario = :id_usuario ORDER BY data_entrada DESC LIMIT 5");
$stmtEntradas->execute([':id_usuario' => $id_usuario]);
$entradas = $stmtEntradas->fetchAll(PDO::FETCH_ASSOC);

$stmtUltimosGastos = $pdo->prepare("SELECT nome_gasto, valor_gasto FROM Gasto WHERE id_usuario = :id_usuario ORDER BY data_gasto DESC LIMIT 5");
$stmtUltimosGastos->execute([':id_usuario' => $id_usuario]);
$ultimosGastos = $stmtUltimosGastos->fetchAll(PDO::FETCH_ASSOC);

$stmtDividas = $pdo->prepare("SELECT nome_divida, taxa_divida FROM Divida WHERE id_usuario = :id_usuario ORDER BY data_vencimento DESC LIMIT 5");
$stmtDividas->execute([':id_usuario' => $id_usuario]);
$ultimasDividas = $stmtDividas->fetchAll(PDO::FETCH_ASSOC);

$stmtMetas = $pdo->prepare("SELECT titulo, valor_meta, previsao_conclusao FROM Meta WHERE id_usuario = :id_usuario ORDER BY previsao_conclusao ASC LIMIT 5");
$stmtMetas->execute([':id_usuario' => $id_usuario]);
$ultimasMetas = $stmtMetas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EasyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">    
    <link rel="stylesheet" href="../../../assets/css/pages/4-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
        <?php include_once('../includes/sidebar.php'); ?>
        <?php include_once('../includes/header.php');?>

        <main>
            <h1>Bem-vindo, <?= htmlspecialchars($nome) ?>!</h1>

            <section class="cards">
                <div class="card">
                    <h3>Saldo Atual</h3>
                    <p>R$<?= number_format($saldo, 2, ',', '.') ?></p>
                </div>
                <div class="card">
                    <h3>Receita Total</h3>
                    <p>R$<?= number_format($receita, 2, ',', '.') ?></p>
                </div>
                <div class="card">
                    <h3>Gastos Totais</h3>
                    <p>R$<?= number_format($gastos, 2, ',', '.') ?></p>
                </div>
            </section>

            <section class="charts">
                <h2>Resumo Financeiro</h2>
                <canvas id="financeChart"></canvas>
            </section>

            <section class="tables">
                <div class="table-box">
                    <h3>Últimas Receitas</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($entradas) > 0) : ?>
                                <?php foreach ($entradas as $entrada) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($entrada['descricao']) ?></td>
                                        <td>R$ <?= number_format($entrada['valor'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2">Nenhuma receita encontrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-box">
                    <h3>Últimos Gastos</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Descrição</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($ultimosGastos) > 0) : ?>
                                <?php foreach ($ultimosGastos as $gasto) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($gasto['nome_gasto']) ?></td>
                                        <td>R$ <?= number_format($gasto['valor_gasto'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2">Nenhum gasto encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="tables">
                <div class="table-box">
                    <h3>Últimas Dívidas</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Taxa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($ultimasDividas) > 0) : ?>
                                <?php foreach ($ultimasDividas as $divida) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($divida['nome_divida']) ?></td>
                                        <td><?= number_format($divida['taxa_divida'], 2, ',', '.') ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="2">Nenhuma dívida registrada.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-box">
                    <h3>Próximas Metas</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Meta</th>
                                <th>Valor</th>
                                <th>Previsão</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($ultimasMetas) > 0) : ?>
                                <?php foreach ($ultimasMetas as $meta) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($meta['titulo']) ?></td>
                                        <td>R$ <?= number_format($meta['valor_meta'], 2, ',', '.') ?></td>
                                        <td><?= date('d/m/Y', strtotime($meta['previsao_conclusao'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3">Nenhuma meta definida.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
        <script>
        // Dados para o Chart.js (passados do PHP para o JavaScript)
        const saldo = <?= json_encode($saldo) ?>;
        const receita = <?= json_encode($receita) ?>;
        const gastos = <?= json_encode($gastos) ?>;

        const ctx = document.getElementById('financeChart').getContext('2d');
        const financeChart = new Chart(ctx, {
            type: 'bar', // Tipo de gráfico: barra
            data: {
                labels: ['Saldo', 'Receita', 'Gastos'], // Rótulos para as barras
                datasets: [{
                    label: 'Visão Financeira Atual', // Título do conjunto de dados
                    data: [saldo, receita, gastos], // Dados do gráfico
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)', // Azul para saldo
                        'rgba(75, 192, 192, 0.6)', // Verde para receitas
                        'rgba(255, 99, 132, 0.6)' // Vermelho para gastos
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1 // Largura da borda das barras
                }]
            },
            options: {
                responsive: true, // Torna o gráfico responsivo
                maintainAspectRatio: false, // Não mantém a proporção original do canvas
                scales: {
                    y: {
                        beginAtZero: true, // Eixo Y começa em zero
                        ticks: {
                            // Formata os valores do eixo Y como moeda BRL
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
                            // Formata os valores das tooltips como moeda BRL
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
    <script src="../../../assets/js/components/sidebar.js"></script>
</body>
</html>