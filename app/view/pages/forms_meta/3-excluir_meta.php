<?php
session_start();
require_once __DIR__ . '/../../../db.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$id_meta = $_POST['id'] ?? 0;

try {
    $db = new DB();
    $pdo = $db->connect();

    $stmt = $pdo->prepare("DELETE FROM meta WHERE id_meta = :id AND id_usuario = :id_usuario");
    $stmt->execute([
        ':id' => $id_meta,
        ':id_usuario' => $id_usuario
    ]);

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
                <h2>META EXCLUÍDA</h2>
                <p>✅ Meta excluída com sucesso!</p>
                <button onclick="window.location.href=\'1-forms_meta.php\'">Fechar</button>
            </div>
        </div>
    </body>
    </html>';
    exit;

    header("Location: 1-forms_meta.php");
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
                <p>❌ Erro ao excluir meta. Tente novamente mais tarde.</p>
                <button onclick="window.location.href=\'1-forms_meta.php\'">Fechar</button>
            </div>
        </div>
    </body>
    </html>';
    exit;
}
