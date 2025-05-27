<?php
// public/dashboard.php

session_start();

// 1. Verificação de autenticação
// O caminho do formulário de login é relativo à raiz do servidor web (public/)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../forms_login/1-forms_login.html");
    exit;
}

// 2. Conexão com o banco de dados
// O caminho do db.php é relativo ao arquivo que o inclui (dashboard.php está em public/, db.php está na raiz do projeto)
require_once __DIR__ . '../../../db.php';

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];

// 3. Obtenção de dados do usuário (necessários para a sidebar e header)
// Busca o nome e perfil do usuário da tabela Usuario
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $usuario_id]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário'; // Usa o nome do DB, com fallback
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido'; // Usa o perfil do DB, com fallback

// 4. Obtenção de dados específicos do Dashboard
// Buscar valores totais de receita
$stmtReceita = $pdo->prepare("SELECT SUM(valor) as total_receita FROM Entrada WHERE id_usuario = :id_usuario");
$stmtReceita->execute([':id_usuario' => $usuario_id]);
$receita = $stmtReceita->fetchColumn() ?? 0;

// Buscar valores totais de gastos
$stmtGastos = $pdo->prepare("SELECT SUM(valor_gasto) as total_gasto FROM Gasto WHERE id_usuario = :id_usuario");
$stmtGastos->execute([':id_usuario' => $usuario_id]);
$gastos = $stmtGastos->fetchColumn() ?? 0;

// Calcular saldo
$saldo = $receita - $gastos;

// Buscar últimas entradas (LIMIT 5)
$stmtEntradas = $pdo->prepare("SELECT descricao, valor FROM Entrada WHERE id_usuario = :id_usuario ORDER BY data_entrada DESC LIMIT 5");
$stmtEntradas->execute([':id_usuario' => $usuario_id]);
$entradas = $stmtEntradas->fetchAll(PDO::FETCH_ASSOC);

// Buscar últimos gastos (LIMIT 5)
$stmtUltimosGastos = $pdo->prepare("SELECT nome_gasto, valor_gasto FROM Gasto WHERE id_usuario = :id_usuario ORDER BY data_gasto DESC LIMIT 5");
$stmtUltimosGastos->execute([':id_usuario' => $usuario_id]);
$ultimosGastos = $stmtUltimosGastos->fetchAll(PDO::FETCH_ASSOC);

// Buscar últimas dívidas (LIMIT 5)
$stmtDividas = $pdo->prepare("SELECT nome_divida, taxa_divida FROM Divida WHERE id_usuario = :id_usuario ORDER BY data_divida DESC LIMIT 5");
$stmtDividas->execute([':id_usuario' => $usuario_id]);
$ultimasDividas = $stmtDividas->fetchAll(PDO::FETCH_ASSOC);

// Buscar próximas metas (LIMIT 5)
$stmtMetas = $pdo->prepare("SELECT titulo, valor_meta, previsao_conclusao FROM Meta WHERE id_usuario = :id_usuario ORDER BY previsao_conclusao ASC LIMIT 5");
$stmtMetas->execute([':id_usuario' => $usuario_id]);
$ultimasMetas = $stmtMetas->fetchAll(PDO::FETCH_ASSOC);

// Buscar tetos de gasto (se houver necessidade de exibir no dashboard)
// $stmtTetos = $pdo->prepare("SELECT nome_teto, valor_teto, valor_atual FROM Teto_gasto WHERE id_usuario = :id_usuario");
// $stmtTetos->execute([':id_usuario' => $usuario_id]);
// $tetosDeGasto = $stmtTetos->fetchAll(PDO::FETCH_ASSOC);

// Buscar despesas (se houver necessidade de exibir no dashboard)
// $stmtDespesas = $pdo->prepare("SELECT nome_despesa, valor_despesa, data_vencimento FROM Despesa WHERE id_usuario = :id_usuario ORDER BY data_vencimento ASC LIMIT 5");
// $stmtDespesas->execute([':id_usuario' => $usuario_id]);
// $proximasDespesas = $stmtDespesas->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EasyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">    
    <link rel="stylesheet" href="../../../assets/css/pages/4-dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <?php include_once __DIR__ . '../includes/sidebar.php'; ?>

        <div class="content-wrapper"> <?php include_once __DIR__ . '../includes/header.php'; ?>

            <main>
                <h1>Bem-vindo, <?= htmlspecialchars($nome) ?>!</h1>
                <p style="font-size: 1.2em; color: #cfcfcf; margin-bottom: 20px;">
                    Seu perfil financeiro: <strong><?= htmlspecialchars($perfilUsuario) ?></strong>
                </p>

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
        </div> </div> <script>
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

        // Lógica da Sidebar: Abre/fecha e ajusta o layout
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const openBtn = document.getElementById('open_btn');
            const openBtnIcon = document.getElementById('open_btn_icon');
            const mainContent = document.querySelector('main'); // Seleciona a tag <main>
            const topHeader = document.getElementById('top-header'); // Seleciona o cabeçalho superior

            // Função para ajustar o padding-left do main e do header
            function adjustContentPadding() {
                if (sidebar.classList.contains('open-sidebar')) {
                    // Sidebar aberta: ajusta margem e padding para a largura expandida
                    mainContent.style.marginLeft = '220px'; // Largura da sidebar aberta
                    topHeader.style.paddingLeft = '270px'; // Largura da sidebar aberta + padding extra
                } else {
                    // Sidebar fechada: ajusta margem e padding para a largura compacta
                    mainContent.style.marginLeft = '82px'; // Largura da sidebar fechada
                    topHeader.style.paddingLeft = '132px'; // Largura da sidebar fechada + padding extra
                }
            }

            // Adiciona listener de clique ao botão de abrir/fechar sidebar
            openBtn.addEventListener('click', function() {
                sidebar.classList.toggle('open-sidebar'); // Alterna a classe 'open-sidebar'
                openBtnIcon.classList.toggle('fa-chevron-right'); // Alterna o ícone da seta
                openBtnIcon.classList.toggle('fa-chevron-left'); // Alterna o ícone da seta
                adjustContentPadding(); // Chama a função para ajustar o layout
            });

            // Chama a função uma vez no carregamento inicial para definir o estado correto
            adjustContentPadding();
        });
    </script>
    <script src="../../../assets/js/components/sidebar.js"></script>
</body>

</html>