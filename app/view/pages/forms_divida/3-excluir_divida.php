<?php
session_start();

if (!isset($_SESSION['id_usuario']) || !isset($_POST['id'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$id_divida = $_POST['id'];
$id_usuario = $_SESSION['id_usuario'];

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("DELETE FROM Divida WHERE id_divida = :id AND id_usuario = :id_usuario");
    $stmt->execute(['id' => $id_divida, 'id_usuario' => $id_usuario]);

    // Alerta customizado
    echo '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Sucesso</title>
        <style>
            body {
                margin: 0;
                font-family: Poppins, sans-serif;
                background-color: rgba(0, 0, 0, 0.5);
            }
            .modal-overlay {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
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
                margin-top: 0;
                font-size: 20px;
                font-weight: bold;
            }
            .custom-alert p {
                margin: 10px 0 20px;
            }
            .custom-alert button {
                background-color: #00b386;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
            }
            .custom-alert button:hover {
                background-color: #009970;
            }
        </style>
    </head>
    <body>
        <div class="modal-overlay">
            <div class="custom-alert">
                <h2>DÍVIDA EXCLUÍDA</h2>
                <p>✅ Dívida excluída com sucesso!</p>
                <button onclick="window.location.href=\'1-forms_divida.php\'">Fechar</button>
            </div>
        </div>
    </body>
    </html>';
    exit;
} catch (PDOException $e) {
       echo '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Erro</title>
        <style>
            body {
                margin: 0;
                font-family: Arial, sans-serif;
                background-color: rgba(0, 0, 0, 0.5);
            }
            .modal-overlay {
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
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
                margin-top: 0;
                font-size: 20px;
                font-weight: bold;
            }
            .custom-alert p {
                margin: 10px 0 20px;
            }
            .custom-alert button {
                background-color: #00b386;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
            }
            .custom-alert button:hover {
                background-color: #009970;
            }
        </style>
    </head>
    <body>
        <div class="modal-overlay">
            <div class="custom-alert">
                <h2>ERRO</h2>
                <p>❌ Erro ao excluir dívida. Tente novamente mais tarde.</p>
                <button onclick="window.location.href=\'1-forms_divida.php\'">Fechar</button>
            </div>
        </div>
    </body>
    </html>';
    exit;
}
