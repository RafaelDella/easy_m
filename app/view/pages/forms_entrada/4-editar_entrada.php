<?php
session_start();

// Verifica se o usuário está logado
// 'id_usuario' é usado para consistência com os outros arquivos
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

require_once __DIR__ . '../../../../db.php'; // Caminho ajustado para consistência

// Conexão com o banco de dados
$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario']; // Alterado para 'id_usuario' para consistência

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

// Verifica se a requisição é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coleta e valida os dados do formulário
    $id = $_POST['id'] ?? null; // ID da entrada a ser atualizada
    $descricao = trim($_POST['descricao'] ?? '');
    $valor = filter_var($_POST['valor'] ?? 0, FILTER_VALIDATE_FLOAT); // Valida como float
    $categoria = $_POST['categoria'] ?? '';
    $data = $_POST['data_entrada'] ?? '';

    // Validação dos campos obrigatórios e do ID
    if (empty($id) || $valor === false || $valor <= 0 || empty($descricao) || empty($categoria) || empty($data)) {
        mostrarAlerta("DADOS INVÁLIDOS", "❌ Por favor, preencha todos os campos corretamente para atualizar a entrada.","1-forms_entrada.php");
    }

    try {
        // Atualiza os dados da entrada no banco
        $sql = "UPDATE Entrada SET descricao = :descricao, valor = :valor, categoria = :categoria, data_entrada = :data 
                 WHERE id_entrada = :id AND id_usuario = :id_usuario";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':descricao' => $descricao,
            ':valor' => $valor,
            ':categoria' => $categoria,
            ':data' => $data,
            ':id' => $id,
            ':id_usuario' => $id_usuario
        ]);

        mostrarAlerta("SUCESSO", "✅ Entrada atualizada com sucesso!", "1-forms_entrada.php");
    } catch (PDOException $e) {
        mostrarAlerta("ERRO", "❌ Erro ao atualizar entrada: Por favor, tente novamente mais tarde.", "1-forms_entrada.php");
    }
} else {
    mostrarAlerta("REQUISIÇÃO INVÁLIDA", "Apenas requisições via POST são permitidas.", "1-forms_entrada.php");
}
?>