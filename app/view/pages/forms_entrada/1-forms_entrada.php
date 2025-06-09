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
$sql = "SELECT * FROM Entrada WHERE id_usuario = :id_usuario";
$params = ['id_usuario' => $id_usuario];

if (!empty($categoria)) {
    $sql .= " AND categoria = :categoria";
    $params['categoria'] = $categoria;
}
if (!empty($mes)) {
    $sql .= " AND MONTH(data_entrada) = :mes";
    $params['mes'] = $mes;
}
if (!empty($ano)) {
    $sql .= " AND YEAR(data_entrada) = :ano";
    $params['ano'] = $ano;
}
if (!empty($busca)) {
    $sql .= " AND (descricao LIKE :busca OR categoria LIKE :busca)";
    $params['busca'] = "%{$busca}%";
}

$sql .= " ORDER BY data_entrada DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$entradas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Financeiro - easy_m</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/10-forms_entrada.css">
</head>
    <body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>
        <main>
            <h1>Gerenciar Entradas</h1>

            <div class="top-bar">
                <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <select name="categoria">
                        <option value="">Selecione a Categoria</option>
                        <option value="Salário" <?= $categoria == 'Salário' ? 'selected' : '' ?>>Salário</option>
                        <option value="Freelance" <?= $categoria == 'Freelance' ? 'selected' : '' ?>>Freelance</option>
                        <option value="Presente" <?= $categoria == 'Presente' ? 'selected' : '' ?>>Presente</option>
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
                    </select>

                    <button type="submit" class="btn yellow">
                        <i class="fa fa-filter"></i> Filtrar
                    </button>
                </form>

                <button type="button" class="btn red" onclick="abrirModalConfirmacao('Confirmar Exclusão', 'Tem certeza que deseja deletar TODAS as entradas? Essa ação não pode ser desfeita.', function() { document.getElementById('form-excluir-todas').submit(); })">
                    <i class="fa-solid fa-delete-left"></i> Excluir Todas as Entradas
                </button>

                <button class="btn green" onclick="abrirModal('modalCadastroEntrada')">
                    <i class="fa-solid fa-square-plus"></i> Cadastrar Entrada
                </button>
            </div>

            <form id="form-excluir-todas" action="6-deletar_todas_entradas.php" method="POST" style="display:none;"></form>

            <?php include '../includes/modal_cadastro_entrada.php'; ?>
            <?php include '../includes/modal_edicao_entrada.php'; ?>
            <?php include '../includes/modal_visualizacao_entrada.php'; ?>
            <?php include '../includes/modal_confirmacao.php'; ?>

            <div class="container">
                <?php if (empty($entradas)): ?>
                    <p>Nenhuma entrada encontrado para os filtros selecionados.</p>
                <?php else: ?>
                    <?php foreach ($entradas as $entrada): ?>
                        <div class="session-card">
                            <div class="session-info">Descrição: <?= htmlspecialchars(limitarTexto($entrada['descricao'])) ?></div>
                            <div class="session-info">Valor: R$ <?= htmlspecialchars(number_format($entrada['valor'], 2, ',', '.')) ?></div>
                            <div class="session-info">Categoria: <?= htmlspecialchars(limitarTexto($entrada['categoria'])) ?></div>
                            <div class="session-info">Data: <?= htmlspecialchars(date('d/m/Y', strtotime($entrada['data_entrada']))) ?></div>

                            <div class="session-actions">
                                <button class="btn purple" onclick="visualizarEntrada(<?= $entrada['id_entrada'] ?>)"><i class="fa-solid fa-eye"></i> Visualizar</button>

                                <button class="btn blue" onclick='editarEntrada(<?= json_encode([
                                    "id" => $entrada["id_entrada"],
                                    "descricao" => $entrada["descricao"],
                                    "valor" => $entrada["valor"],
                                    "categoria" => $entrada["categoria"],
                                    "data_entrada" => $entrada["data_entrada"]
                                ]) ?>)'><i class="fa-solid fa-pen-to-square"></i> Alterar</button>

                                <form action="3-excluir_entrada.php" method="POST" style="display:inline;" onsubmit="event.preventDefault(); abrirModalConfirmacao('Confirmar Exclusão', 'Tem certeza que deseja excluir esta entrada?', function() { event.target.submit(); })">
                                    <input type="hidden" name="id" value="<?= $entrada['id_entrada'] ?>">
                                    <button class="btn red"><i class="fa-solid fa-trash"></i> Excluir</button>
                                </form>
                            </div>
                        </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
        </main>
        <script src="../../../assets/js/components/global.js"></script> </body>
        <script src="../../../assets/js/components/sidebar.js"></script>
    </body>

</html>