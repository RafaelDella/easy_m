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

// Buscar nome e perfil do usuário
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

function limitarTexto($texto, $limite = 20) {
    return strlen($texto) > $limite ? substr($texto, 0, $limite) . '...' : $texto;
}

// Filtros
$categoria = $_GET['categoria'] ?? '';
$mes       = $_GET['mes'] ?? '';
$ano       = $_GET['ano'] ?? '';

// SQL com filtros
$sql = "SELECT * FROM meta WHERE id_usuario = :id_usuario";
$params = ['id_usuario' => $id_usuario];

if (!empty($categoria)) {
    $sql .= " AND categoria = :categoria";
    $params['categoria'] = $categoria;
}
if (!empty($mes)) {
    $sql .= " AND MONTH(previsao_conclusao) = :mes";
    $params['mes'] = $mes;
}
if (!empty($ano)) {
    $sql .= " AND YEAR(previsao_conclusao) = :ano";
    $params['ano'] = $ano;
}

$sql .= " ORDER BY previsao_conclusao ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Minhas Metas - EasyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/20-forms_meta.css">
    <script src="../../../assets/js/pages/11-forms_metas.js"></script> 

</head>
<body>
<?php include_once('../includes/sidebar.php'); ?>
<?php include_once('../includes/header.php'); ?>

<main>
    <h1>Gerenciar Metas</h1>

    <div class="top-bar">
        <form method="GET" style="display:flex; flex-wrap:wrap; gap:10px;">
            <input type="text" name="categoria" placeholder="Filtrar por categoria" value="<?= htmlspecialchars($categoria) ?>">

            <select name="mes">
                <option value="">Mês</option>
                <?php
                foreach (range(1, 12) as $m) {
                    $val = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $sel = $mes == $val ? 'selected' : '';
                    echo "<option value=\"$val\" $sel>" . DateTime::createFromFormat('!m', $m)->format('F') . "</option>";
                }
                ?>
            </select>

            <select name="ano">
                <option value="">Ano</option>
                <?php
                $anoAtual = date("Y");
                for ($a = $anoAtual; $a >= $anoAtual - 10; $a--) {
                    $sel = $ano == $a ? 'selected' : '';
                    echo "<option value=\"$a\" $sel>$a</option>";
                }
                ?>
            </select>

            <button class="btn yellow" type="submit">
                <i class="fa fa-filter"></i> Filtrar
            </button>
        </form>

        <button type="button" class="btn red" onclick="abrirModalConfirmacao('Confirmar Exclusão', 'Tem certeza que deseja deletar TODAS as metas? Essa ação não pode ser desfeita.', function() { document.getElementById('formExcluirTodas').submit()})">
            <i class="fa-solid fa-delete-left"></i> Excluir Todas as Metas
        </button>

        <button class="btn green" onclick="abrirModal('modalCadastroMeta')">
            <i class="fa fa-plus"></i> Cadastrar Meta
        </button>
    </div>

    <form id="formExcluirTodas" action="6-deletar_todas_metas.php" method="POST" style="display:none;"></form>

    <?php include '../includes/modal_cadastro_meta.php'; ?>
    <?php include '../includes/modal_edicao_meta.php'; ?>
    <?php include '../includes/modal_visualizacao_meta.php'; ?>
    <?php include '../includes/modal_confirmacao.php'; ?>

    <div class="container">
        <?php if (empty($metas)): ?>
            <p>Nenhuma meta encontrada.</p>
        <?php else: ?>
            <?php foreach ($metas as $meta): ?>
                <div class="session-card">
                    <div class="session-info"><strong>Título:</strong> <?= limitarTexto($meta['titulo']) ?></div>
                    <div class="session-info"><strong>Categoria:</strong> <?= htmlspecialchars($meta['categoria']) ?></div>
                    <div class="session-info"><strong>Valor:</strong> R$ <?= number_format($meta['valor_meta'], 2, ',', '.') ?></div>
                    <div class="session-info"><strong>Previsão:</strong> <?= date('d/m/Y', strtotime($meta['previsao_conclusao'])) ?></div>

                    <div class="session-actions">
                        <button class="btn purple" onclick="visualizarMeta(<?= $meta['id_meta'] ?>)">
                            <i class="fa fa-eye"></i> Visualizar
                        </button>

                        <button class="btn blue" onclick='editarMeta(<?= json_encode([
                            "id" => $meta["id_meta"],
                            "titulo" => $meta["titulo"],
                            "descricao" => $meta["descricao"],
                            "categoria" => $meta["categoria"],
                            "valor_meta" => $meta["valor_meta"],
                            "previsao_conclusao" => $meta["previsao_conclusao"]
                        ]) ?>)'>
                            <i class="fa fa-pen-to-square"></i> Editar
                        </button>

                        <form action="3-excluir_meta.php" method="POST" onsubmit="event.preventDefault(); abrirModalConfirmacao('Excluir Meta', 'Deseja excluir esta meta?', () => event.target.submit())">
                            <input type="hidden" name="id" value="<?= $meta['id_meta'] ?>">
                            <button class="btn red"><i class="fa fa-trash"></i> Excluir</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<script src="../../../assets/js/components/global.js"></script>
<script src="../../../assets/js/components/sidebar.js"></script>
</body>
</html>
