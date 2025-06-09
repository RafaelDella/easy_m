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

// Validação básica dos dados POST
$descricao = $_POST['descricao'] ?? '';
$valor = filter_var($_POST['valor'] ?? 0, FILTER_VALIDATE_FLOAT);
$categoria = $_POST['categoria'] ?? '';
$data_entrada = $_POST['data_entrada'] ?? '';

// Função para exibir alerta estilizado
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

// Verificar se os dados obrigatórios estão preenchidos e são válidos
if (empty($descricao) || $valor === false || $valor <= 0 || empty($categoria) || empty($data_entrada)) {
    mostrarAlerta("CAMPOS INVÁLIDOS", "❌ Por favor, preencha todos os campos corretamente para cadastrar o entrada.", "1-forms_entrada.php");
}

try {
    $stmt = $pdo->prepare("INSERT INTO Entrada (descricao, valor, categoria, data_entrada, id_usuario)
                           VALUES (:descricao, :valor, :categoria, :data_entrada, :id_usuario)");

    $stmt->execute([
        'descricao' => $descricao,
        'valor' => $valor,
        'categoria' => $categoria,
        'data_entrada' => $data_entrada,
        'id_usuario' => $id_usuario
    ]);

    mostrarAlerta("SUCESSO", "✅ Entrada registrada com sucesso!", "1-forms_entrada.php");

} catch (PDOException $e) {
    echo "Erro ao registrar entrada: " . $e->getMessage();
}
