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

// Buscar nome e perfil do usuário (se ainda não estiver em header.php)
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Função para limitar tamanho do texto
function limitarTexto($texto, $limite = 10) {
    return strlen($texto) > $limite ? substr($texto, 0, $limite) . '...' : $texto;
}

// Filtros
$categoria = $_GET['categoria'] ?? '';
$mes       = $_GET['mes'] ?? '';
$ano       = $_GET['ano'] ?? '';
$busca     = trim($_GET['busca'] ?? '');

// Montar SQL com filtros
$sql = "SELECT * FROM Gasto WHERE id_usuario = :id_usuario";
$params = ['id_usuario' => $id_usuario];

if (!empty($categoria)) {
    $sql .= " AND categoria_gasto = :categoria";
    $params['categoria'] = $categoria;
}
if (!empty($mes)) {
    $sql .= " AND MONTH(data_gasto) = :mes";
    $params['mes'] = $mes;
}
if (!empty($ano)) {
    $sql .= " AND YEAR(data_gasto) = :ano";
    $params['ano'] = $ano;
}
if (!empty($busca)) {
    $sql .= " AND (nome_gasto LIKE :busca OR desc_gasto LIKE :busca OR categoria_gasto LIKE :busca)";
    $params['busca'] = "%{$busca}%";
}

$sql .= " ORDER BY data_gasto DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gerenciar Gastos - easy_m</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/12-forms_gasto.css"> </head>
<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>
    <main>
        <h1>Gerenciar Gastos</h1>

        <div class="top-bar">
            <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="categoria">
                    <option value="">Selecione a Categoria</option>
                    <option value="Alimentação" <?= $categoria == 'Alimentação' ? 'selected' : '' ?>>Alimentação</option>
                    <option value="Transporte" <?= $categoria == 'Transporte' ? 'selected' : '' ?>>Transporte</option>
                    <option value="Moradia" <?= $categoria == 'Moradia' ? 'selected' : '' ?>>Moradia</option>
                    <option value="Saúde" <?= $categoria == 'Saúde' ? 'selected' : '' ?>>Saúde</option>
                    <option value="Educação" <?= $categoria == 'Educação' ? 'selected' : '' ?>>Educação</option>
                    <option value="Lazer" <?= $categoria == 'Lazer' ? 'selected' : '' ?>>Lazer</option>
                    <option value="Contas" <?= $categoria == 'Contas' ? 'selected' : '' ?>>Contas</option>
                    <option value="Compras" <?= $categoria == 'Compras' ? 'selected' : '' ?>>Compras</option>
                    <option value="Outro" <?= $categoria == 'Outro' ? 'selected' : '' ?>>Outro</option>
                </select>

                <select name="mes">
                    <option value="">Selecione o mês</option>
                    <?php
                    $meses = [
                        "01" => "Janeiro", "02" => "Fevereiro", "03" => "Março",
                        "04" => "Abril", "05" => "Maio", "06" => "Junho",
                        "07" => "Julho", "08" => "Agosto", "09" => "Setembro",
                        "10" => "Outubro", "11" => "Novembro", "12" => "Dezembro"
                    ];
                    foreach ($meses as $num => $nomeMes) {
                        $sel = ($mes == $num) ? 'selected' : '';
                        echo "<option value=\"$num\" $sel>$nomeMes</option>";
                    }
                    ?>
                </select>

                <select name="ano" id="ano">
                    <option value="">Selecione o ano</option>
                    <?php
                    $anoAtual = date('Y');
                    $selAnoAtual = ($anoAtual == $ano) ? 'selected' : '';
                    echo "<option value=\"$anoAtual\" $selAnoAtual>$anoAtual</option>";

                    for ($a = $anoAtual - 1; $a >= 2000; $a--) {
                        $sel = ($a == $ano) ? 'selected' : '';
                        echo "<option value=\"$a\" $sel>$a</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn yellow">
                        <i class="fa fa-filter"></i> Filtrar
                </button>
            </form>

            <button type="button" class="btn red" onclick="abrirModalConfirmacao('Confirmar Exclusão', 'Tem certeza que deseja deletar TODOS os gastos? Essa ação não pode ser desfeita.', function() { document.getElementById('form-excluir-todos-gastos').submit(); })">
                <i class="fa-solid fa-delete-left"></i> Excluir Todos os Gastos
            </button>

            <button class="btn green" onclick="abrirModal('modalCadastroGasto')">
                <i class="fa-solid fa-square-plus"></i> Cadastrar Gasto
            </button>
        </div>

        <form id="form-excluir-todos-gastos" action="6-deletar_todos_gastos.php" method="POST" style="display:none;"></form>

        <?php include '../includes/modal_cadastro_gasto.php'; ?>
        <?php include '../includes/modal_edicao_gasto.php'; ?>
        <?php include '../includes/modal_visualizacao_gasto.php'; ?>
        <?php include '../includes/modal_confirmacao.php'; ?>

        <div class="container">
            <?php if (empty($gastos)): ?>
                <p>Nenhum gasto encontrado para os filtros selecionados.</p>
            <?php else: ?>
                <?php foreach ($gastos as $gasto): ?>
                    <div class="session-card">
                        <div class="session-info">Nome: <?= htmlspecialchars(limitarTexto($gasto['nome_gasto'])) ?></div>
                        <div class="session-info">Descrição: <?= htmlspecialchars(limitarTexto($gasto['desc_gasto'])) ?></div>
                        <div class="session-info">Categoria: <?= htmlspecialchars(limitarTexto($gasto['categoria_gasto'])) ?></div>
                        <div class="session-info">Valor: R$ <?= htmlspecialchars(number_format($gasto['valor_gasto'], 2, ',', '.')) ?></div>
                        <div class="session-info">Imprevisto: <?= $gasto['is_imprevisto'] ? 'Sim' : 'Não' ?></div>
                        <div class="session-info">Data: <?= htmlspecialchars(date('d/m/Y', strtotime($gasto['data_gasto']))) ?></div>

                        <div class="session-actions">
                            <button class="btn purple" onclick="visualizarGasto(<?= $gasto['id_gasto'] ?>)"><i class="fa-solid fa-eye"></i> Visualizar</button>

                            <button class="btn blue" onclick='editarGasto(<?= json_encode([
                                "id" => $gasto["id_gasto"],
                                "nome_gasto" => $gasto["nome_gasto"],
                                "desc_gasto" => $gasto["desc_gasto"],
                                "categoria_gasto" => $gasto["categoria_gasto"],
                                "valor_gasto" => $gasto["valor_gasto"],
                                "is_imprevisto" => $gasto["is_imprevisto"],
                                "data_gasto" => $gasto["data_gasto"]
                            ]) ?>)'><i class="fa-solid fa-pen-to-square"></i> Alterar</button>

                            <form action="3-excluir_gasto.php" method="POST" style="display:inline;" onsubmit="event.preventDefault(); abrirModalConfirmacao('Confirmar Exclusão', 'Tem certeza que deseja excluir este gasto?', function() { event.target.submit(); })">
                                <input type="hidden" name="id" value="<?= $gasto['id_gasto'] ?>">
                                <button class="btn red"><i class="fa-solid fa-trash"></i> Excluir</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
    <script src="../../../assets/js/components/global.js"></script>
    <script src="../../../assets/js/components/sidebar.js"></script>
    <script src="../../../assets/js/pages/6-forms_gasto.js"></script> 
</body>
</html>