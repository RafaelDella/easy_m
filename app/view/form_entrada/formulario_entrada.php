<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../formulario_login/form_login.html");
    exit;
}

require_once '../../db.php';

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];
$nome = $_SESSION['usuario_nome'];

$stmtPerfil = $pdo->prepare("SELECT perfil FROM Usuario WHERE id = :id");
$stmtPerfil->execute([':id' => $usuario_id]);
$perfilUsuario = $stmtPerfil->fetchColumn();

function limitarTexto($texto, $limite = 10) {
    return strlen($texto) > $limite ? substr($texto, 0, $limite) . '...' : $texto;
}

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

$sql = "SELECT * FROM Entrada WHERE id_usuario = :usuario_id";
$params = ['usuario_id' => $_SESSION['usuario_id']];

if ($busca !== '') {
    $sql .= " AND (descricao LIKE :busca OR categoria LIKE :busca)";
    $params['busca'] = '%' . $busca . '%';
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$entradas = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cadastro de Sessão</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/form_entrada/formulario_entrada.css">
</head>

<body>
    <nav id="sidebar">
        <div id="sidebar_content">
            <div id="user">
                <a href="perfil_usuario/perfil_usuario.html" title="Editar Perfil">
                    <img src="../../assets/image/zeca.jpg" id="user_avatar" alt="Avatar">
                </a>
                <p id="user_infos">
                    <span class="item-description">
                        <?= htmlspecialchars($nome) ?>
                    </span>
                    <span class="item-description">
                        <?= htmlspecialchars($perfilUsuario ?? 'Não definido') ?>
                    </span>
                </p>
            </div>

            <ul id="side_items">
                <li class="side-item active">
                    <a href="../dashboard.php"><i class="fa-solid fa-chart-line"></i><span
                            class="item-description">Painel</span></a>
                </li>
                <li class="side-item"><a href="../view/extrato_page/extrato_view.php"><i
                            class="fa-solid fa-file-invoice"></i><span class="item-description">Extrato</span></a></li>
                <li class="side-item"><a href="formulario_entrada.php"><i
                            class="fa-solid fa-hand-holding-dollar"></i><span class="item-description">Nova
                            Entrada</span></a></li>
                <li class="side-item"><a href="../view/formulario_gasto/forms_gasto.html"><i
                            class="fa-solid fa-sack-xmark"></i><span class="item-description">Novo Gasto</span></a></li>
                <li class="side-item"><a href="../view/formulario_divida/index.php"><i
                            class="fa-solid fa-cash-register"></i><span class="item-description">Nova Dívida</span></a>
                </li>
                <li class="side-item"><a href="../view/calculo_quitacao_divida/calcular_quitacao.html"><i
                            class="fa-solid fa-calculator"></i><span class="item-description">Calculadora de
                            Quitação</span></a></li>
                <li class="side-item"><a href="../view/formulário_perfil/forms_perfil.html"><i
                            class="fa-solid fa-user"></i><span class="item-description">Teste de Perfil</span></a></li>
                <li class="side-item"><a href="../view/formulário_perfil/forms_perfil.html"><i class="fa-solid fa-pager"></i><span class="item-description">Analisar Dívida</span></a></li>
                <li class="side-item"><a href="../view/formulário_perfil/forms_perfil.html"><i
                            class="fa-solid fa-circle-dollar-to-slot"></i><span class="item-description">Teto de
                            Gasto</span></a></li>
                <li class="side-item"><a href="../view/formulário_perfil/forms_perfil.html"><i
                            class="fa-solid fa-chart-pie"></i><span class="item-description">Gráfico de Pizza</span></a>
                </li>
                <li class="side-item"><a href="../view/formulário_perfil/forms_perfil.html"><i
                            class="fa-solid fa-comments-dollar"></i><span class="item-description">Fórum</span></a></li>
                <li class="side-item"><a href="../view/formulario_login/form_login.html"><i
                            class="fa-solid fa-right-from-bracket"></i><span class="item-description">Logout</span></a>
                </li>
            </ul>

            <button id="open_btn"><i id="open_btn_icon" class="fa-solid fa-chevron-right"></i></button>
        </div>
    </nav>

    <main>
        <h1>Cadastrar Entrada</h1>

        <div class="top-bar">
            <select>
                <option>Selecione o curso</option>
            </select>
            <select>
                <option>Selecione a disciplina</option>
            </select>
            <input type="text" id="campo-busca" placeholder="Procurar...">
            <button type="submit">🔍</button>
            <form action="deletar_todas_entradas.php" method="POST" onsubmit="return confirm('Tem certeza que deseja deletar TODOS os itens? Esta ação não pode ser desfeita!');" style="display:inline;">
                <button class="btn red" type="submit">
                    <i class="fa-solid fa-delete-left"></i> Deletar todos os itens
                </button>
            </form>
            <button class="btn green" onclick="abrirModal()"><i class="fa-solid fa-square-plus"></i> Cadastrar entrada</button>
        </div>

        <div id="modalEntrada" class="modal">
            <div class="modal-content">
                <span class="close" onclick="fecharModal()">&times;</span>
                
                <h2>Nova Entrada</h2> 

                <form action="salvar_entrada.php" method="POST" onsubmit="return validarFormulario()">
                    <input type="hidden" name="id" id="entrada_id">

                    <label for="descricao">Descrição:</label>
                    <input type="text" id="descricao" name="descricao" required>

                    <label for="valor">Valor (R$):</label>
                    <input type="number" id="valor" name="valor" step="0.01" required>

                    <label for="categoria">Categoria:</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">Selecione</option>
                        <option value="Salário">Salário</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Presente">Presente</option>
                        <option value="Outro">Outro</option>
                    </select>

                    <label for="data_entrada">Data:</label>
                    <input type="date" id="data_entrada" name="data_entrada" required>

                    <div class="modal-buttons">
                        <button type="submit" id="btn-submit">Salvar Entrada</button>
                        <button type="button" onclick="fecharModal()" class="btn-cancelar">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalEditarEntrada" class="modal">
            <div class="modal-content">
                <span class="close" onclick="fecharModalEditar()">&times;</span>
        
                <h2>Editar Entrada</h2> 

                <form action="editar_entrada.php" method="POST" onsubmit="return validarFormularioEditar()">
                    <input type="hidden" name="id" id="editar_entrada_id">

                    <label for="editar_descricao">Descrição:</label>
                    <input type="text" id="editar_descricao" name="descricao" required>

                    <label for="editar_valor">Valor (R$):</label>
                    <input type="number" id="editar_valor" name="valor" step="0.01" required>

                    <label for="editar_categoria">Categoria:</label>
                    <select id="editar_categoria" name="categoria" required>
                        <option value="">Selecione</option>
                        <option value="Salário">Salário</option>
                        <option value="Freelance">Freelance</option>
                        <option value="Presente">Presente</option>
                        <option value="Outro">Outro</option>
                    </select>

                    <label for="editar_data_entrada">Data:</label>
                    <input type="date" id="editar_data_entrada" name="data_entrada" required>

                    <div class="modal-buttons">
                        <button type="submit" id="btn-submit">Salvar Alterações</button>
                        <button type="button" onclick="fecharModalEditar()" class="btn-cancelar">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalVisualizar" class="modal">
            <div class="modal-content">
                <span class="close" onclick="fecharModalVisualizar()">&times;</span>
                <h2>Detalhes da Entrada</h2>

                <p><strong>Descrição:</strong> <span id="verDescricao"></span></p>
                <p><strong>Valor:</strong> <span id="verValor"></span></p>
                <p><strong>Categoria:</strong> <span id="verCategoria"></span></p>
                <p><strong>Data:</strong> <span id="verDataEntrada"></span></p>

                <button onclick="fecharModalVisualizar()" class="btn-cancelar">Fechar</button>
            </div>
        </div>
        
        <div class="container"> 
            <?php
                $stmt = $pdo->prepare("SELECT * FROM Entrada WHERE id_usuario = :usuario_id ORDER BY data_entrada DESC");
                $stmt->execute(['usuario_id' => $usuario_id]);
                $entradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($entradas as $entrada): ?>
                    <div class="session-card">
                                    <div class="session-header">
                                Entrada: <?= htmlspecialchars($entrada['id_entrada']) ?>
                            </div>

                            <div class="session-info">
                                Descrição: <?= htmlspecialchars(limitarTexto($entrada['descricao'], 10)) ?>
                            </div>

                            <div class="session-info">
                                Valor: R$ <?= htmlspecialchars(limitarTexto(number_format($entrada['valor'], 2, ',', '.'), 10)) ?>
                            </div>

                            <div class="session-info">
                                Categoria: <?= htmlspecialchars(limitarTexto($entrada['categoria'], 10)) ?>
                            </div>

                            <div class="session-info">
                                Data: <?= htmlspecialchars(limitarTexto(date('d/m/Y', strtotime($entrada['data_entrada'])), 10)) ?>
                            </div>

                        <div class="session-actions">

                            <button class="btn purple" onclick="visualizarEntrada(<?= $entrada['id_entrada'] ?>)"><i class="fa-solid fa-eye"></i> Visualizar Entrada</button>

                            <button class="btn blue"
                                onclick='editarEntrada(
                            <?= json_encode([
                                "id" => $entrada["id_entrada"],
                                "descricao" => $entrada["descricao"],
                                "valor" => $entrada["valor"],
                                "categoria" => $entrada["categoria"],
                                "data_entrada" => $entrada["data_entrada"]
                            ]) ?>
                        )'> <i class="fa-solid fa-pen-to-square"></i> Alterar</button>

                            <form action="excluir_entrada.php" method="POST" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta entrada?');">
                                <input type="hidden" name="id" value="<?= $entrada['id_entrada'] ?>">
                                <button class="btn red"> <i class="fa-solid fa-trash"></i> Excluir</button>
                                
                            </form>
                        </div>
                    </div>
            <?php endforeach; ?>
        </div>

    </main>

    <script src="../../assets/form_entrada/formulario_entrada.js"></script>

</body>

</html>