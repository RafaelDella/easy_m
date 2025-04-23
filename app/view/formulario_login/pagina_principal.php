<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: form_login.html");
    exit;
}

// Dados fictícios (substitua com consulta ao banco)
$nome = $_SESSION['usuario_nome'];
$saldo = 0.00;
$receita = 0.00;
$gastos = 0.00;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo ao EasyM</title>
    <link rel="stylesheet" href="pagina_principal.css">
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <h2>📊 EasyM</h2>
        <nav>
            <ul>
                <li><strong>Painel</strong></li>
                <li><a href="#">+ Receitas</a></li>
                <li><a href="#">– Gastos</a></li>
                <li><a href="#">🎯 Metas</a></li>
                <li><a href="logout.php">🚪 Sair</a></li>
            </ul>
        </nav>
    </aside>

    <main class="content">
        <header class="header">
            <h1><?= htmlspecialchars($nome) ?></h1>
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
                <p>-R$<?= number_format($gastos, 2, ',', '.') ?></p>
            </div>
        </section>

        <section class="graph">
            <h3>Visão Geral da Saúde Financeira</h3>
            <div class="placeholder-grafico">[Gráfico]</div>
        </section>

        <section class="tables">
            <div class="table-box">
                <h3>Receitas</h3>
                <table>
                    <thead>
                        <tr><th>Fonte</th><th>Valor</th></tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">Nenhuma receita encontrada</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="table-box">
                <h3>Gastos</h3>
                <table>
                    <thead>
                        <tr><th>Descrição</th><th>Valor</th></tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="2">Nenhum gasto encontrado</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

</body>
</html>
