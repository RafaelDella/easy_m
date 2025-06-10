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

// Função de alerta estilizado
function mostrarAlerta($titulo, $mensagem, $redirect)
{
    echo "
    <!DOCTYPE html>
    <html lang='pt-br'>
    <head>
        <meta charset='UTF-8'>
        <title>Alerta</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: Poppins, sans-serif;
                background-color: rgba(0, 0, 0, 0.6);
            }
            .modal-overlay {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.6);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }
            .custom-alert {
                background-color: #1e1e1e;
                color: white;
                padding: 30px;
                border-radius: 10px;
                width: 300px;
                text-align: center;
                box-shadow: 0 0 10px rgba(0,0,0,0.4);
            }
            .custom-alert h2 {
                font-size: 20px;
                margin-bottom: 10px;
            }
            .custom-alert p {
                margin: 10px 0 20px;
                font-size: 14px;
            }
            .custom-alert button {
                background-color: #00b386;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
                transition: background-color 0.3s ease;
            }
            .custom-alert button:hover {
                background-color: #009970;
            }
        </style>
    </head>
    <body>
        <div class='modal-overlay'>
            <div class='custom-alert'>
                <h2>$titulo</h2>
                <p>$mensagem</p>
                <button onclick=\"window.location.href='$redirect'\">Fechar</button>
            </div>
        </div>
    </body>
    </html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $nome_divida = trim($_POST['nome_divida'] ?? '');
    $valor_total = filter_var($_POST['valor_total'] ?? 0, FILTER_VALIDATE_FLOAT);
    $valor_pago = filter_var($_POST['valor_pago'] ?? 0, FILTER_VALIDATE_FLOAT);
    $taxa = filter_var($_POST['taxa_divida'] ?? 0, FILTER_VALIDATE_FLOAT);
    $categoria = $_POST['categoria_divida'] ?? '';
    $data_vencimento = $_POST['data_vencimento'] ?? '';

    if (
        empty($id) || empty($nome_divida) ||
        $valor_total === false || $valor_total <= 0 ||
        $valor_pago === false || $valor_pago < 0 ||
        $taxa === false || $taxa < 0 ||
        empty($categoria) || empty($data_vencimento)
    ) {
        mostrarAlerta("DADOS INVÁLIDOS", "❌ Por favor, preencha todos os campos corretamente para atualizar a dívida.", "1-forms_divida.php");
    }

    try {
        $sql = "UPDATE Divida 
                SET nome_divida = :nome, valor_total = :total, valor_pago = :pago, 
                    taxa_divida = :taxa, categoria_divida = :categoria, data_vencimento = :vencimento
                WHERE id_divida = :id AND id_usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome_divida,
            ':total' => $valor_total,
            ':pago' => $valor_pago,
            ':taxa' => $taxa,
            ':categoria' => $categoria,
            ':vencimento' => $data_vencimento,
            ':id' => $id,
            ':id_usuario' => $id_usuario
        ]);

        mostrarAlerta("SUCESSO", "✅ Dívida atualizada com sucesso!", "1-forms_divida.php");
    } catch (PDOException $e) {
        mostrarAlerta("ERRO", "❌ Erro ao atualizar dívida: Por favor, tente novamente mais tarde.", "1-forms_divida.php");
    }
} else {
    mostrarAlerta("REQUISIÇÃO INVÁLIDA", "Apenas requisições via POST são permitidas.", "1-forms_divida.php");
}
