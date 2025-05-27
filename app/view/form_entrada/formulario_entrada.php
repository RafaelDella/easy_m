<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../forms_login/form_login.html");
    exit;
}

require_once '../../db.php';

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];
$nome = $_SESSION['usuario_nome'];

// Buscar perfil do usuário
$stmtPerfil = $pdo->prepare("SELECT perfil FROM Usuario WHERE id = :id");
$stmtPerfil->execute([':id' => $usuario_id]);
$perfilUsuario = $stmtPerfil->fetchColumn();

// Função para limitar tamanho do texto
function limitarTexto($texto, $limite = 10) {
    return strlen($texto) > $limite ? substr($texto, 0, $limite) . '...' : $texto;
}
// Pegar filtros
$categoria = $_GET['categoria'] ?? '';
$mes       = $_GET['mes'] ?? '';
$ano       = $_GET['ano'] ?? '';
$busca     = trim($_GET['busca'] ?? '');

// Montar SQL com filtros
$sql = "SELECT * FROM Entrada WHERE usuario_id = :usuario_id";
$params = ['usuario_id' => $usuario_id];

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
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro de Sessão</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/form_entrada/forms_entrada.css">
</head>

<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div id="sidebar_content">
            <div id="user">
                <a href="../perfil_usuario/perfil_usuario.html" title="Editar Perfil">
                    <img src="../../assets/image/zeca.jpg" id="user_avatar" alt="Avatar">
                </a>
                <p id="user_infos">
                    <span class="item-description"><?= htmlspecialchars($nome) ?></span>
                    <span class="item-description"><?= htmlspecialchars($perfilUsuario ?? 'Não definido') ?></span>
                </p>
            </div>

            <!-- Menu lateral -->
            <ul id="side_items">
                <li class="side-item"><a href="../dashboard.php"><i class="fa-solid fa-chart-line"></i><span class="item-description">Painel</span></a></li>
                <li class="side-item"><a href="../view/extrato_page/extrato_view.php"><i class="fa-solid fa-file-invoice"></i><span class="item-description">Extrato</span></a></li>
                <li class="side-item active"><a href="forms_entrada.php"><i class="fa-solid fa-hand-holding-dollar"></i><span class="item-description">Nova Entrada</span></a></li>
                <li class="side-item"><a href="../view/forms_gasto/forms_gasto.html"><i class="fa-solid fa-sack-xmark"></i><span class="item-description">Novo Gasto</span></a></li>
                <li class="side-item"><a href="../view/forms_divida/index.php"><i class="fa-solid fa-cash-register"></i><span class="item-description">Nova Dívida</span></a></li>
                <li class="side-item"><a href="../view/calculo_quitacao_divida/calcular_quitacao.html"><i class="fa-solid fa-calculator"></i><span class="item-description">Calculadora de Quitação</span></a></li>
                <li class="side-item"><a href="../view/forms_perfil/forms_perfil.html"><i class="fa-solid fa-user"></i><span class="item-description">Teste de Perfil</span></a></li>
                <li class="side-item"><a href="../view/forms_perfil/forms_perfil.html"><i class="fa-solid fa-pager"></i><span class="item-description">Analisar Dívida</span></a></li>
                <li class="side-item"><a href="../view/forms_perfil/forms_perfil.html"><i class="fa-solid fa-circle-dollar-to-slot"></i><span class="item-description">Teto de Gasto</span></a></li>
                <li class="side-item"><a href="../view/forms_perfil/forms_perfil.html"><i class="fa-solid fa-chart-pie"></i><span class="item-description">Gráfico de Pizza</span></a></li>
                <li class="side-item"><a href="../view/forms_perfil/forms_perfil.html"><i class="fa-solid fa-comments-dollar"></i><span class="item-description">Fórum</span></a></li>
                <li class="side-item"><a href="../view/forms_login/form_login.html"><i class="fa-solid fa-right-from-bracket"></i><span class="item-description">Logout</span></a></li>
            </ul>

            <button id="open_btn"><i id="open_btn_icon" class="fa-solid fa-chevron-right"></i></button>
        </div>
    </nav>

    <header id="top-header">
        <div class="logo-container">
            <img src="../../assets/image/logo.jpeg" alt="Logo" class="logo-img">
            <span class="logo-text">EASY M</span>
        </div>
    </header>

    <!-- Conteúdo principal -->
    <main>
        <h1>Cadastrar Entrada</h1>

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
                    <?php
                    $anoAtual = date('Y');
                    for ($i = $anoAtual; $i >= 2000; $i--) {
                        $sel = ($ano == $i) ? 'selected' : '';
                        echo "<option value=\"$i\" $sel>$i</option>";
                    }
                    ?>
                </select>

                <button type="submit" class="btn yellow">
                    <i class="fa-solid fa-magnifying-glass"></i> Pesquisar
                </button>
            </form>


            <div id="confirmModal" class="modal" style="display: none;">
                <div class="modal-content">
                    <h3>Tem certeza que deseja deletar TODOS os itens?</h3>
                    <p>Essa ação não pode ser desfeita.</p>
                    <div class="modal-buttons">
                    <button class="cancelar" onclick="fecharModalExcluir()">Cancelar</button>
                    <form action="deletar_todas_entradas.php" method="POST">
                        <button type="submit" class="confirmar">Confirmar exclusão</button>
                    </form>
                    </div>
                </div>
            </div>


            <!-- Botão abre o modal -->
            <form id="form-excluir-todas" action="deletar_todas_entradas.php" method="POST">
                <button type="button" class="btn red" onclick="abrirModalExcluir()">
                    <i class="fa-solid fa-delete-left"></i> Excluir Todas as Entradas
                </button>
            </form>

            <button class="btn green" onclick="abrirModal()">
                <i class="fa-solid fa-square-plus"></i> Cadastrar Entrada
            </button>
        </div>

        <!-- Modal de cadastro -->
        <?php include '../modais/modal_entrada.php'; ?>
        <?php include '../modais/modal_editar_entrada.php'; ?>
        <?php include '../modais/modal_visualizar_entrada.php'; ?>

        <!-- Lista de entradas -->
        <div class="container">
            <?php foreach ($entradas as $entrada): ?>
                <div class="session-card">
                    <div class="session-header">Entrada: <?= htmlspecialchars($entrada['id_entrada']) ?></div>
                    <div class="session-info">Descrição: <?= htmlspecialchars(limitarTexto($entrada['descricao'])) ?></div>
                    <div class="session-info">Valor: R$ <?= htmlspecialchars(number_format($entrada['valor'], 2, ',', '.')) ?></div>
                    <div class="session-info">Categoria: <?= htmlspecialchars(limitarTexto($entrada['categoria'])) ?></div>
                    <div class="session-info">Data: <?= htmlspecialchars(date('d/m/Y', strtotime($entrada['data_entrada']))) ?></div>

                    <div class="session-actions">
                        <button class="btn purple" onclick="visualizarEntrada(<?= $entrada['id_entrada'] ?>)"><i class="fa-solid fa-eye"></i> Visualizar Entrada</button>

                        <button class="btn blue" onclick='editarEntrada(<?= json_encode([
                            "id" => $entrada["id_entrada"],
                            "descricao" => $entrada["descricao"],
                            "valor" => $entrada["valor"],
                            "categoria" => $entrada["categoria"],
                            "data_entrada" => $entrada["data_entrada"]
                        ]) ?>)'><i class="fa-solid fa-pen-to-square"></i> Alterar</button>

                        <form action="excluir_entrada.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta entrada?');">
                            <input type="hidden" name="id" value="<?= $entrada['id_entrada'] ?>">
                            <button class="btn red"><i class="fa-solid fa-trash"></i> Excluir</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="../../assets/form_entrada/forms_entrada.js"></script>
</body>
</html>
