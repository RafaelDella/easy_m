<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

require_once __DIR__ . '/../../../db.php';
$db = new DB();
$pdo = $db->connect();

$id_usuario = $_SESSION['id_usuario'];

$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

function limitarTexto($texto, $limite = 10)
{
    return strlen($texto) > $limite ? substr($texto, 0, $limite) . '...' : $texto;
}

$categoria = $_GET['categoria'] ?? '';
$busca     = trim($_GET['busca'] ?? '');

$sql = "SELECT * FROM Divida WHERE id_usuario = :id_usuario";
$params = ['id_usuario' => $id_usuario];

if (!empty($categoria)) {
    $sql .= " AND categoria_divida = :categoria";
    $params['categoria'] = $categoria;
}
if (!empty($busca)) {
    $sql .= " AND (nome_divida LIKE :busca OR categoria_divida LIKE :busca)";
    $params['busca'] = "%{$busca}%";
}

$sql .= " ORDER BY data_vencimento ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$dividas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gerenciar Dívidas - easy_m</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/11-forms_divida.css">
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>
        <h1>Gerenciar Dívidas</h1>

        <div class="top-bar">
            <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="categoria">
                    <option value="">Categoria</option>
                    <option value="Cartão" <?= $categoria == 'Cartão' ? 'selected' : '' ?>>Cartão</option>
                    <option value="Empréstimo" <?= $categoria == 'Empréstimo' ? 'selected' : '' ?>>Empréstimo</option>
                    <option value="Financiamento" <?= $categoria == 'Financiamento' ? 'selected' : '' ?>>Financiamento</option>
                    <option value="Outro" <?= $categoria == 'Outro' ? 'selected' : '' ?>>Outro</option>
                </select>

                <input type="text" name="busca" placeholder="Buscar dívida" value="<?= htmlspecialchars($busca) ?>">

                <button type="submit" class="btn yellow">
                    <i class="fa fa-filter"></i> Filtrar
                </button>
            </form>

            <button type="button" class="btn red" onclick="abrirModalConfirmacao('Confirmar Exclusão', 'Tem certeza que deseja deletar TODAS as dívidas? Essa ação não pode ser desfeita.', function() { document.getElementById('form-excluir-todas').submit(); })">
                <i class="fa-solid fa-delete-left"></i> Excluir Todas as Dívidas
            </button>

            <button class="btn green" onclick="abrirModal('modalCadastroDivida')">
                <i class="fa-solid fa-square-plus"></i> Cadastrar Dívida
            </button>
        </div>

        <form id="form-excluir-todas" action="6-deletar_todas_dividas.php" method="POST" style="display:none;"></form>

        <?php include '../includes/modal_cadastro_divida.php'; ?>
        <?php include '../includes/modal_edicao_divida.php'; ?>
        <?php include '../includes/modal_visualizacao_divida.php'; ?>
        <?php include '../includes/modal_confirmacao.php'; ?>

        <div class="container">
            <?php if (empty($dividas)): ?>
                <p>Nenhuma dívida encontrada para os filtros selecionados.</p>
            <?php else: ?>
                <?php foreach ($dividas as $d): ?>
                    <div class="session-card">
                        <div class="session-info"><strong>Nome:</strong> <?= limitarTexto($d['nome_divida']) ?></div>
                        <div class="session-info"><strong>Valor:</strong> R$ <?= number_format($d['valor_total'], 2, ',', '.') ?></div>
                        <div class="session-info"><strong>Pago:</strong> R$ <?= number_format($d['valor_pago'], 2, ',', '.') ?></div>
                        <div class="session-info"><strong>Categoria:</strong> <?= $d['categoria_divida'] ?></div>
                        <div class="session-info"><strong>Vencimento:</strong> <?= date('d/m/Y', strtotime($d['data_vencimento'])) ?></div>

                        <div class="session-actions">
                            <button class="btn purple" onclick="visualizarDivida(<?= $d['id_divida'] ?>)">
                                <i class="fa-solid fa-eye"></i> Visualizar
                            </button>

                            <button class="btn blue" onclick='editarDivida(<?= json_encode($d) ?>)'>
                                <i class="fa-solid fa-pen-to-square"></i> Alterar
                            </button>

                            <form action="3-excluir_divida.php" method="POST" style="display:inline;" onsubmit="event.preventDefault(); abrirModalConfirmacao('Confirmar Exclusão', 'Tem certeza que deseja excluir esta dívida?', function() { event.target.submit(); })">
                                <input type="hidden" name="id" value="<?= $d['id_divida'] ?>">
                                <button class="btn red"><i class="fa-solid fa-trash"></i> Excluir</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script src="../../../assets/js/components/sidebar.js"></script>
    <script src="../../../assets/js/components/global.js"></script>
    <script src="../../../assets/js/pages/8-forms_divida.js"></script>
</body>

</html>