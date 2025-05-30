<?php
// Desativa a exibição de erros na tela para evitar que corrompam a saída JSON.
// Os erros ainda serão registrados nos logs do servidor.
ini_set('display_errors', 0);
error_reporting(E_ALL); // Mantém o registro de todos os erros.

session_start();

// Define o cabeçalho Content-Type para JSON antes de qualquer outra saída.
header('Content-Type: application/json');

// Verifica se o usuário está logado e se o ID do gasto foi fornecido.
if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    // Se a requisição for inválida, retorna uma mensagem de erro em JSON.
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida ou usuário não logado.']);
    exit; // Termina a execução do script.
}

$id_usuario = $_SESSION['id_usuario']; // Obtém o ID do usuário da sessão.
$id = $_GET['id']; // Obtém o ID do gasto da URL.

// Inclui o arquivo de conexão com o banco de dados.
// Certifique-se de que este arquivo (db.php) também não tenha espaços em branco antes ou depois das tags PHP.
require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect(); // Conecta-se ao banco de dados.

try {
    // Prepara a consulta SQL para buscar o gasto pelo ID do gasto e ID do usuário.
    $stmt = $pdo->prepare("SELECT * FROM Gasto WHERE id_gasto = :id AND id_usuario = :id_usuario");
    // Executa a consulta com os parâmetros.
    $stmt->execute(['id' => $id, 'id_usuario' => $id_usuario]);
    // Busca o resultado como um array associativo.
    $gasto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gasto) {
        // Se o gasto for encontrado, retorna sucesso e os dados do gasto em JSON.
        echo json_encode(['sucesso' => true, 'gasto' => $gasto]);
    } else {
        // Se o gasto não for encontrado ou o usuário não tiver permissão, retorna uma mensagem de erro.
        echo json_encode(['sucesso' => false, 'mensagem' => 'Gasto não encontrado ou você não tem permissão para acessá-lo.']);
    }
} catch (PDOException $e) {
    // Em caso de erro no banco de dados, retorna uma mensagem de erro interno.
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno ao buscar gasto.']);
    // Opcional: Para depuração em ambiente de desenvolvimento, você pode logar o erro.
    // error_log("Erro ao buscar gasto: " . $e->getMessage());
}

?>