<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

require_once __DIR__ . '../../../../db.php';
$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario'];

// Dados do usuário para o header/sidebar
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Dados financeiros reais do banco
$stmtValores = $pdo->prepare("
    SELECT
        COALESCE(SUM(e.valor), 0) AS saldoAtual,
        COALESCE(SUM(d.valor_total), 0) AS dividaTotal,
        COALESCE(SUM(d.valor_pago), 0) AS dividaPaga
    FROM Usuario u
    LEFT JOIN Entrada e ON u.id_usuario = e.id_usuario
    LEFT JOIN Divida d ON u.id_usuario = d.id_usuario
    WHERE u.id_usuario = :id_usuario
");
$stmtValores->execute(['id_usuario' => $id_usuario]);
$result = $stmtValores->fetch(PDO::FETCH_ASSOC);

$saldoAtual = $result['saldoAtual'];
$dividaTotal = $result['dividaTotal'];
$dividaPaga = $result['dividaPaga'];
$dividaRestante = $dividaTotal - $dividaPaga;

// Despesas futuras (exemplo fixo ou puxar futuramente de uma tabela real)
$despesasFuturas = 300.00;
$saldoLivre = $saldoAtual - $dividaRestante - $despesasFuturas;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Financeiro - EasyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/5-forms_perfil.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main style="padding: 2rem; text-align: center;">
        <h2 style="color: #fff;">Distribuição do Saldo Atual</h2>
        <canvas id="graficoPizza" width="300" height="300"></canvas>
        <p style="margin-top: 1rem; color: #ccc;">
            Dívida total: <strong>R$ <?= number_format($dividaTotal, 2, ',', '.') ?></strong> |
            Já paga: <strong>R$ <?= number_format($dividaPaga, 2, ',', '.') ?></strong>
        </p>
    </main>

    <script>
        const ctx = document.getElementById('graficoPizza').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Dívida pendente', 'Despesas futuras', 'Saldo livre'],
                datasets: [{
                    label: 'Distribuição',
                    data: [<?= $dividaRestante ?>, <?= $despesasFuturas ?>, <?= $saldoLivre ?>],
                    backgroundColor: ['#e74c3c', '#f39c12', '#2ecc71'],
                    borderColor: '#1a1a1a',
                    borderWidth: 1
                }]
            },
            options: {
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
                            label: function(context) {
                                const valor = context.raw.toLocaleString('pt-BR', {
                                    style: 'currency',
                                    currency: 'BRL'
                                });
                                return `${context.label}: ${valor}`;
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