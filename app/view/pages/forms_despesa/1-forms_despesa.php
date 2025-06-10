<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

// Obter dados do usuário para sidebar/header
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Função para limitar o texto
function limitarTexto($texto, $limite = 10) {
    return strlen($texto) > $limite ? substr($texto, 0, $limite) . '...' : $texto;
}

// Filtros para despesas
$categoria_id = $_GET['categoria'] ?? '';
$mes          = $_GET['mes'] ?? '';
$ano          = $_GET['ano'] ?? '';
$busca        = trim($_GET['busca'] ?? '');

// Montar SQL com filtros
// Corrigido: Verifique se a coluna id_categoria existe na sua tabela Despesa.
// O erro anterior "Unknown column 'd.id_categoria'" sugeria que ela poderia estar faltando.
$sql = "SELECT d.*, cd.nome_categoria FROM Despesa d JOIN CategoriaDespesa cd ON d.id_categoria = cd.id_categoria WHERE d.id_usuario = :id_usuario";
$params = ['id_usuario' => $id_usuario];

if (!empty($categoria_id)) {
    $sql .= " AND d.id_categoria = :id_categoria";
    $params['id_categoria'] = $categoria_id;
}
if (!empty($mes)) {
    $sql .= " AND MONTH(d.data_vencimento) = :mes";
    $params['mes'] = $mes;
}
if (!empty($ano)) {
    $sql .= " AND YEAR(d.data_vencimento) = :ano";
    $params['ano'] = $ano;
}
if (!empty($busca)) {
    $sql .= " AND (d.nome_despesa LIKE :busca OR d.descricao LIKE :busca OR cd.nome_categoria LIKE :busca)";
    $params['busca'] = "%{$busca}%";
}

$sql .= " ORDER BY d.data_vencimento DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$despesas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obter categorias de despesas para o filtro e modais (APENAS DO USUÁRIO LOGADO)
$stmtCategorias = $pdo->prepare("SELECT id_categoria, nome_categoria FROM CategoriaDespesa WHERE id_usuario = :id_usuario ORDER BY nome_categoria");
$stmtCategorias->execute([':id_usuario' => $id_usuario]);
$categoriasDespesa = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Despesas - easy_m</title>
    <link rel="icon" type="image/x-icon" href="/easy_m/assets/image/favicon.ico"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/16-forms_despesa.css">
</head>
<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>
        <h1>Gerenciar Despesas</h1>

        <div class="top-bar">
            <form method="GET" action="" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <select name="categoria">
                    <option value="">Selecione a Categoria</option>
                    <?php foreach ($categoriasDespesa as $cat) : ?>
                        <option value="<?= htmlspecialchars($cat['id_categoria']) ?>" <?= $categoria_id == $cat['id_categoria'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['nome_categoria']) ?>
                        </option>
                    <?php endforeach; ?>
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
                    for ($i = $anoAtual; $i >= 2000; $i--) {
                        $sel = ($ano == $i) ? 'selected' : '';
                        echo "<option value=\"$i\" $sel>$i</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn yellow">
                        <i class="fa fa-filter"></i> Filtrar
                </button>

            </form>

            
            <button class="btn purple" onclick="abrirModalCategorias()">
                <i class="fa-solid fa-tags"></i> Gerenciar Categorias
            </button>


            <div id="confirmModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h3>Tem certeza que deseja deletar TODAS as despesas?</h3>
                    <p>Essa ação não pode ser desfeita.</p>
                    <div class="modal-buttons">
                        <button class="btn red" onclick="fecharModalExcluir()">Cancelar</button>
                        <form action="6-deletar_todas_despesas.php" method="POST">
                            <button type="submit" class="btn green">Confirmar exclusão</button>
                        </form>
                    </div>
                </div>
            </div>

            <button type="button" class="btn red" onclick="abrirModalExcluir()">
                <i class="fa-solid fa-delete-left"></i> Excluir Todas as Despesas
            </button>

            <button class="btn green" onclick="abrirModal()">
                <i class="fa-solid fa-square-plus"></i> Cadastrar Despesa
            </button>
        </div>

        <?php include_once('../includes/modal_cadastro_despesa.php'); ?>
        <?php include_once('../includes/modal_edicao_despesa.php'); ?>
        <?php include_once('../includes/modal_visualizacao_despesa.php'); ?>

        <div id="categoriasModal" class="modal">
            <div class="modal-content">
                <span class="close-button" onclick="fecharModalCategorias()">&times;</span>
                <h2>Gerenciar Categorias de Despesas</h2>

                <div class="categoria-form">
                    <input type="text" id="nova_categoria_nome" placeholder="Nome da nova categoria" required>
                    <button class="btn green" id="btn_adicionar_categoria"><i class="fa-solid fa-plus"></i> Adicionar Categoria</button>
                </div>

                <div class="categoria-lista">
                    <h3>Categorias Cadastradas:</h3>
                    <ul id="lista_categorias">
                        </ul>
                </div>

                <div class="modal-buttons">
                    <button type="button" class="btn blue" onclick="fecharModalCategorias()">Fechar</button>
                </div>
            </div>
        </div>


        <div class="container">
            <?php if (empty($despesas)) : ?>
                <p>Nenhuma despesa encontrada com os filtros aplicados.</p>
            <?php else : ?>
                <?php foreach ($despesas as $despesa) : ?>
                    <div class="session-card">
                        <div class="session-info">Nome: <?= htmlspecialchars(limitarTexto($despesa['nome_despesa'], 20)) ?></div>
                        <div class="session-info">Valor: R$ <?= htmlspecialchars(number_format($despesa['valor_despesa'], 2, ',', '.')) ?></div>
                        <div class="session-info">Vencimento: <?= htmlspecialchars(date('d/m/Y', strtotime($despesa['data_vencimento']))) ?></div>
                        <div class="session-info">Categoria: <?= htmlspecialchars($despesa['nome_categoria']) ?></div>
                    

                        <div class="session-actions">
                            <button class="btn purple" onclick="visualizarDespesa(<?= $despesa['id_despesa'] ?>)">
                                <i class="fa-solid fa-eye"></i> Visualizar
                            </button>

                            <button class="btn blue" onclick='editarDespesa(<?= json_encode([
                                "id"           => $despesa["id_despesa"],
                                "nome"         => $despesa["nome_despesa"],
                                "descricao"    => $despesa["descricao"],
                                "valor"        => $despesa["valor_despesa"],
                                "data_vencimento" => $despesa["data_vencimento"],
                                "id_categoria" => $despesa["id_categoria"]
                            ]) ?>)'>
                                <i class="fa-solid fa-pen-to-square"></i> Alterar
                            </button>

                            <form action="3-excluir_despesa.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta despesa?');">
                                <input type="hidden" name="id" value="<?= $despesa['id_despesa'] ?>">
                                <button class="btn red">
                                    <i class="fa-solid fa-trash"></i> Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script src="../../../assets/js/components/sidebar.js"></script>
    <script src="../../../assets/js/components/modal.js"></script>
    <script src="../../../assets/js/pages/7-forms_despesa.js"></script>
</body>
</html>