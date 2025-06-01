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

$stmtTeto = $pdo->prepare("SELECT * FROM Teto_gasto WHERE id_usuario = ? ORDER BY categoria ASC");
$stmtTeto->execute([$id_usuario]);
$tetos = $stmtTeto->fetchAll(PDO::FETCH_ASSOC);

// Buscar gastos do mês por categoria
$gastosMes = [];
foreach ($tetos as $teto) {
    $categoria = mb_strtolower($teto['categoria'], 'UTF-8'); // Normaliza a string no PHP

    $stmtGasto = $pdo->prepare("
        SELECT COALESCE(SUM(valor_gasto), 0) AS total
        FROM Gasto
        WHERE id_usuario = :id 
          AND LOWER(categoria_gasto) = :cat
          AND MONTH(data_gasto) = MONTH(CURDATE())
          AND YEAR(data_gasto) = YEAR(CURDATE())
    ");

    $stmtGasto->execute([
        ':id' => $id_usuario,
        ':cat' => $categoria
    ]);

    $gastosMes[$teto['categoria']] = floatval($stmtGasto->fetchColumn()); // chave original preservada
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Tetos de Gasto</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/21-forms_teto_gasto.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>
    <main>
        <h2>Tetos de Gasto por Categoria</h2>

        <?php if (empty($tetos)): ?>
            <p>Você ainda não definiu nenhum teto de gasto.</p>
        <?php else: ?>
            <div class="grafico-container">
                <?php foreach ($tetos as $index => $teto):
                    $cat = $teto['categoria'];
                    $gasto = $gastosMes[$cat] ?? 0;
                    $restante = max(0, $teto['valor_teto'] - $gasto);
                    $porcentagem = $teto['valor_teto'] > 0 ? round(($gasto / $teto['valor_teto']) * 100, 2) : 0;
                    $ultrapassou = $gasto > $teto['valor_teto'];
                ?>
                    <div class="teto-box">
                        <h3><?= htmlspecialchars(ucfirst($cat)) ?></h3>
                        <p><strong>Teto:</strong> R$ <?= number_format($teto['valor_teto'], 2, ',', '.') ?></p>
                        <p><strong>Gasto Atual:</strong> R$ <?= number_format($gasto, 2, ',', '.') ?></p>
                        <p><strong>Utilizado:</strong> <?= $porcentagem ?>%</p>
                        <p class="alerta"><?= $ultrapassou ? "⚠️ Ultrapassou o limite!" : "✅ Dentro do limite." ?></p>
                        <canvas id="grafico<?= $index ?>"></canvas>
                        <script>
                            new Chart(document.getElementById('grafico<?= $index ?>'), {
                                type: 'doughnut',
                                data: {
                                    labels: ['Gasto Atual', 'Restante'],
                                    datasets: [{
                                        data: [<?= $gasto ?>, <?= $restante ?>],
                                        backgroundColor: ['#00cc88', '#333']
                                    }]
                                },
                                options: {
                                    plugins: {
                                        title: {
                                            display: false
                                        },
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                        </script>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <!-- Botão flutuante para abrir o modal -->
    <button class="btn-flutuante" onclick="abrirModal()">+ Novo Teto</button>

    <!-- Modal -->
    <div class="modal-overlay" id="modalTeto">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-icon-wrapper info"><i class="fa-solid fa-wallet"></i></div>
                <div class="modal-title">Novo Teto</div>
                <div class="modal-header-message">Defina um teto para uma categoria existente.</div>
            </div>
            <form method="post" action="2-salvar_teto_gasto.php">
                <label for="categoria">Categoria:</label>
                <select name="categoria" required>
                    <?php
                    $stmtCategorias = $pdo->prepare("SELECT DISTINCT categoria_gasto FROM Gasto WHERE id_usuario = ?");
                    $stmtCategorias->execute([$id_usuario]);
                    foreach ($stmtCategorias->fetchAll(PDO::FETCH_COLUMN) as $cat) {
                        echo "<option value=\"" . htmlspecialchars($cat) . "\">" . htmlspecialchars(ucfirst($cat)) . "</option>";
                    }
                    ?>
                </select>

                <label for="nome_teto">Nome:</label>
                <input type="text" name="nome_teto" required>

                <label for="descricao">Descrição:</label>
                <textarea name="descricao" rows="2"></textarea>

                <label for="valor_teto">Valor (R$):</label>
                <input type="number" name="valor_teto" step="0.01" min="0.01" required>

                <div class="modal-footer" style="margin-top: 20px;">
                    <button type="submit" class="modal-button modal-button-primary">Salvar</button>
                    <button type="button" class="modal-button" onclick="fecharModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JS do modal -->
    <script>
        const modal = document.getElementById('modalTeto');

        function abrirModal() {
            modal.classList.add('active');
        }

        function fecharModal() {
            modal.classList.remove('active');
        }
    </script>
    <script src="../../../assets/js/components/sidebar.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script> <!-- Substitua pelo seu -->
</body>

</html>