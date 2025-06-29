<?php
session_start();
require_once __DIR__ . '/../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$categoria = $_POST['categoria'] ?? '';
$valor_meta = $_POST['valor_meta'] ?? 0;
$previsao = $_POST['previsao_conclusao'] ?? '';

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

try {
    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("INSERT INTO meta (titulo, descricao, categoria, valor_meta, previsao_conclusao, id_usuario) 
                           VALUES (:titulo, :descricao, :categoria, :valor_meta, :previsao, :id_usuario)");

    $stmt->execute([
        ':titulo' => $titulo,
        ':descricao' => $descricao,
        ':categoria' => $categoria,
        ':valor_meta' => $valor_meta,
        ':previsao' => $previsao,
        ':id_usuario' => $id_usuario
    ]);

    mostrarAlerta("SUCESSO", "✅ Meta registrada com sucesso!", "1-forms_meta.php");

} catch (PDOException $e) {
    mostrarAlerta("ERRO", "❌ Erro ao registrar meta. Tente novamente mais tarde.", "1-forms_meta.php");
}
