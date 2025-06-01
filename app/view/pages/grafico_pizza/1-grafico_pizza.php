<?php
session_start();

// 1. Verificação de autenticação
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

// 2. Conexão com o banco de dados
require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

$id_usuario = $_SESSION['id_usuario'];

// 3. Obtenção de dados do usuário (para header e sidebar)
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Buscar todas as dívidas do usuário
$stmt = $pdo->prepare("SELECT id_divida, nome_divida, valor_total, valor_pago FROM Divida WHERE id_usuario = :id");
$stmt->execute([':id' => $id_usuario]);
$dividas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Progresso das Dívidas - EasyM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css" />
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css" />
    <link rel="stylesheet" href="../../../assets/css/components/modal.css" />
    <link rel="stylesheet" href="../../../assets/css/pages/13-grafico_pizza.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>
        <?php if (count($dividas) === 0): ?>
            <p class="mensagem-vazia">Você não possui dívidas cadastradas para exibir o progresso.</p>
            <a href="../forms_divida/1-forms_divida.php" class="botao-cadastrar">Cadastrar Dívida</a>
        <?php else: ?>

            <h1>Progresso das Dívidas</h1>
            <div class="container-donuts">
                <?php foreach ($dividas as $divida):
                    $restante = max(0, $divida['valor_total'] - $divida['valor_pago']);
                    $id_canvas = "graficoDoughnut" . $divida['id_divida'];
                ?>
                    <div class="donut-card container">
                        <h3><?= htmlspecialchars($divida['nome_divida']) ?></h3>
                        <canvas id="<?= $id_canvas ?>"></canvas>
                        <p>Total: R$ <?= number_format($divida['valor_total'], 2, ',', '.') ?></p>
                        <p>Pago: R$ <?= number_format($divida['valor_pago'], 2, ',', '.') ?></p>
                        <p>Pendente: R$ <?= number_format($restante, 2, ',', '.') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script src="../../../assets/js/components/sidebar.js"></script>
    <script>
        <?php foreach ($dividas as $divida):
            $restante = max(0, $divida['valor_total'] - $divida['valor_pago']);
            $id_canvas = "graficoDoughnut" . $divida['id_divida'];
        ?>
            new Chart(document.getElementById('<?= $id_canvas ?>').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Pago', 'Pendente'],
                    datasets: [{
                        data: [<?= $divida['valor_pago'] ?>, <?= $restante ?>],
                        backgroundColor: ['#2ecc71', '#7f8c8d'],
                        borderColor: '#1a1a1a',
                        borderWidth: 1
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            labels: {
                                color: '#fff',
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => {
                                    const val = ctx.raw.toLocaleString('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL'
                                    });
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const perc = ((ctx.raw / total) * 100).toFixed(2);
                                    return `${ctx.label}: ${val} (${perc}%)`;
                                }
                            }
                        }
                    }
                }
            });
        <?php endforeach; ?>
    </script>
</body>

</html>