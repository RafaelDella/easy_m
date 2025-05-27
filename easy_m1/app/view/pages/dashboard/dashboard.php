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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">

        <nav id="sidebar">
            <div id="sidebar_content">
                <div id="user">
                    <a href="perfil_usuario/perfil_usuario.html" title="Editar Perfil">
                        <img src="../assets/image/zeca.jpg" id="user_avatar" alt="Avatar">
                    </a>
                    <p id="user_infos">
                        <span class="item-description"><?= htmlspecialchars($nome) ?></span>
                        <span class="item-description"><?= htmlspecialchars($perfilUsuario ?? 'Não definido') ?></span>
                    </p>
                </div>

                <ul id="side_items">
                    <li class="side-item active">
                        <a href="dashboard.php">
                            <i class="fa-solid fa-chart-line"></i>
                            <span class="item-description">Painel</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/extrato_page/extrato_view.php">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span class="item-description">Extrato</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/form_entrada/formulario_entrada.php">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                            <span class="item-description">Nova Entrada</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/formulario_gasto/forms_gasto.html">
                            <i class="fa-solid fa-sack-xmark"></i>
                            <span class="item-description">Novo Gasto</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/formulario_divida/index.php">
                            <i class="fa-solid fa-cash-register"></i>
                            <span class="item-description">Nova dívida</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/formulário_perfil/forms_perfil.html">
                            <i class="fa-solid fa-user"></i>
                            <span class="item-description">Teste de Perfil</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/formulário_perfil/forms_perfil.html">
                            <i class="fa-solid fa-user"></i>
                            <span class="item-description">Análisar dívida</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/formulário_perfil/forms_perfil.html">
                            <i class="fa-solid fa-circle-dollar-to-slot"></i>
                            <span class="item-description">Teto de gasto</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/calculo_quitacao_divida/calcular_quitacao.html">
                            <i class="fa-solid fa-calculator"></i>
                            <span class="item-description">Calculadora de quitação</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/formulário_perfil/forms_perfil.html">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span class="item-description">Gráfico de pizza</span>
                        </a>
                    </li>

                    <li class="side-item">
                        <a href="../view/formulário_perfil/forms_perfil.html">
                            <i class="fa-solid fa-comments-dollar"></i>
                            <span class="item-description">Fórum</span>
                        </a>
                    </li>



                    <li class="side-item">
                        <a href="../view/formulario_login/form_login.html">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span class="item-description">Logout</span>
                        </a>
                    </li>
                </ul>

                <button id="open_btn">
                    <i id="open_btn_icon" class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </nav>

        <main class="content">
            <header class="header">
                <h1>Bem-vindo, <?= htmlspecialchars($nome) ?>!</h1>
                <p style="font-size: 1.2em; color: #555;">
                    Perfil Financeiro: <strong><?= htmlspecialchars($perfilUsuario ?? 'Não definido') ?></strong>
                </p>
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
                        'rgba(255, 99, 132, 0.6)' // Vermelho para gastos
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

        document.getElementById('open_btn').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open-sidebar');
        });
    </script>

</body>

</html>