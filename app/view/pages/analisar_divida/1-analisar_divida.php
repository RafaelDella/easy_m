<?php
// Início da sessão e verificação de autenticação
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

// Conexão com o banco de dados
require_once __DIR__ . '../../../../db.php';
$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario'];

// Dados do usuário (nome e perfil para header/sidebar)
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Verifica se há dívidas
$stmt = $pdo->prepare("SELECT * FROM Divida WHERE id_usuario = :id_usuario ORDER BY data_vencimento ASC");
$stmt->execute(['id_usuario' => $id_usuario]);
$dividas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Resumo das dívidas
$stmtResumo = $pdo->prepare("
    SELECT
        COUNT(*) AS quantidade,
        SUM(valor_total) AS total,
        AVG(valor_total) AS media,
        SUM(valor_total - valor_pago) AS saldo_restante
    FROM Divida
    WHERE id_usuario = :id_usuario
");
$stmtResumo->execute(['id_usuario' => $id_usuario]);
$resumo = $stmtResumo->fetch(PDO::FETCH_ASSOC);

// Receitas totais
$stmtReceitas = $pdo->prepare("SELECT SUM(valor) FROM Entrada WHERE id_usuario = :id_usuario");
$stmtReceitas->execute(['id_usuario' => $id_usuario]);
$totalReceitas = $stmtReceitas->fetchColumn();
$porcentagem = $totalReceitas > 0 ? ($resumo['saldo_restante'] / $totalReceitas) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Análise de Dívidas - EasyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/17-analisar_divida.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>

        <?php if (count($dividas) === 0): ?>
            <p class="mensagem-vazia">Não há dívidas para analisar.</p>
            <a href="../forms_divida/1-forms_divida.php" class="botao-cadastrar">Cadastrar Dívida</a>
        <?php else: ?>
            <div class="container">
                <section class="resumo">
                    <h2>Resumo das Dívidas</h2>
                    <p>Total: R$ <?= number_format($resumo['total'], 2, ',', '.') ?></p>
                    <p>Quantidade: <?= $resumo['quantidade'] ?></p>
                    <p>Média: R$ <?= number_format($resumo['media'], 2, ',', '.') ?></p>
                    <p>Porcentagem da renda comprometida: <?= number_format($porcentagem, 1, ',', '.') ?>%</p>
                </section>

                <section class="grafico-categorias">
                    <h2>Distribuição por Categoria</h2>
                    <canvas id="graficoCategorias"></canvas>
                </section>

                <section class="lista-dividas">
                    <h2>Lista de Dívidas (por vencimento)</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Categoria</th>
                                <th>Valor Total</th>
                                <th>Vencimento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dividas as $divida): ?>
                                <tr>
                                    <td><?= htmlspecialchars($divida['nome_divida']) ?></td>
                                    <td><?= htmlspecialchars($divida['categoria_divida']) ?></td>
                                    <td>R$ <?= number_format($divida['valor_total'], 2, ',', '.') ?></td>
                                    <td><?= date('d/m/Y', strtotime($divida['data_vencimento'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>

                <section class="exportar">
                    <button onclick="window.print()">Exportar Análise (PDF)</button>
                </section>
            </div>
        <?php endif; ?>

    </main>

    <script src="../../../assets/js/components/sidebar.js"></script>
    <script src="../../../assets/js/pages/10-analisar_divida.js"></script>
</body>

</html>