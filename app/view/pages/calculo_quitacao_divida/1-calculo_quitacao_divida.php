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

$stmtDividas = $pdo->prepare("SELECT id_divida, nome_divida, categoria_divida, valor_total, valor_pago, taxa_divida, data_vencimento FROM Divida WHERE id_usuario = :id_usuario");
$stmtDividas->execute([':id_usuario' => $id_usuario]);
$dividas = $stmtDividas->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Simulador de Quitação - EasyM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/9-calculo_quitacao_divida.css">
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>
        <div class="form-container">
            <h2>Simulador de Quitação de Dívida</h2>

            <form id="debtForm">
                <label for="dividaSelecionada">Selecione uma dívida cadastrada:</label>
                <select id="dividaSelecionada">
                    <option value="">--- Escolha uma dívida existente ou preencha manualmente ---</option>
                    <?php foreach ($dividas as $divida): ?>
                        <option value="<?= htmlspecialchars(json_encode($divida)) ?>">
                            <?= htmlspecialchars($divida['nome_divida']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="tipoDivida">Tipo de Dívida:</label>
                <select id="tipoDivida" required>
                    <option value="">Selecione</option>
                    <option value="Cartão de Crédito">Cartão de Crédito</option>
                    <option value="Empréstimo Pessoal">Empréstimo Pessoal</option>
                    <option value="Cheque Especial">Cheque Especial</option>
                    <option value="Outros">Outros</option>
                </select>

                <label for="valorTotal">Valor total da dívida (R$):</label>
                <input type="number" id="valorTotal" step="0.01" min="0" required>

                <label for="valorPago">Valor já pago (R$):</label>
                <input type="number" id="valorPago" step="0.01" min="0" readonly>

                <label for="juros">Valor dos juros (%):</label>
                <input type="number" id="juros" step="0.01" min="0" required>

                <label for="tipoJuros">Tipo de juros:</label>
                <select id="tipoJuros" required>
                    <option value="mensal">Mensal</option>
                    <option value="anual">Anual</option>
                </select>

                <label for="modoQuitacao">Como deseja quitar?</label>
                <select id="modoQuitacao" required>
                    <option value="valor_parcela">Com valor fixo de parcelas</option>
                    <option value="tempo_quitacao">Informar em quanto tempo deseja quitar</option>
                </select>

                <div id="campoValorParcela">
                    <label for="valorParcela">Valor da parcela (R$):</label>
                    <input type="number" id="valorParcela" step="0.01" min="0">
                </div>

                <div id="campoTempo" class="hidden">
                    <label for="tempo">Tempo para quitar:</label>
                    <input type="number" id="tempo" min="1">
                    <select id="unidadeTempo">
                        <option value="meses">Meses</option>
                        <option value="anos">Anos</option>
                    </select>
                </div>

                <button type="submit">Calcular Previsão</button>
                <button type="button" class="btn-excluir-conta" onclick="document.getElementById('debtForm').reset()">Limpar</button>
                <a href="../dashboard/1-dashboard.php" class="voltar-link">← Voltar para o Painel</a>
            </form>
        </div>

        <!-- POPUP -->
        <div id="popupResultado" class="popup hidden">
            <div class="popup-content">
                <span class="close-btn" id="fecharPopup">&times;</span>
                <h2>Resultado da Previsão</h2>
                <p id="resultadoTexto"></p>
                <div class="popup-buttons">
                    <button id="recalcularBtn">Recalcular Previsão</button>
                </div>
            </div>
        </div>
    </main>



    <script src="../../../assets/js/components/sidebar.js"></script>
    <script src="../../../assets/js/pages/9-calculo_quitacao_divida.js"></script>
</body>

</html>