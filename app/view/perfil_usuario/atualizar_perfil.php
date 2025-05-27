<?php
session_start();
require_once '../../db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
<<<<<<< Updated upstream
    header("Location: ../formularioulario_login/formulario_login.html");
=======
    header("Location: ../forms_login/form_login.html");
>>>>>>> Stashed changes
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

<<<<<<< Updated upstream
// Captura os dados enviados via POST
$nome = $_POST['nome'];
$email = $_POST['email'];
$usuario = $_POST['usuario'];
$cpf = $_POST['cpf'];
$escolaridade = $_POST['escolaridade'];
$data_nascimento = $_POST['data_nascimento'];
=======
// Verifica se os dados foram enviados corretamente
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura os dados do forms
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $escolaridade = $_POST['escolaridade'] ?? '';
    $data_nascimento = $_POST['data_nascimento'] ?? '';
>>>>>>> Stashed changes

$db = new DB();
$pdo = $db->connect();

try {
    $stmt = $pdo->prepare("UPDATE Usuario SET 
        nome = :nome, 
        email = :email, 
        usuario = :usuario, 
        cpf = :cpf, 
        escolaridade = :escolaridade, 
        data_nascimento = :data_nascimento 
        WHERE id = :id");

    $stmt->execute([
        'nome' => $nome,
        'email' => $email,
        'usuario' => $usuario,
        'cpf' => $cpf,
        'escolaridade' => $escolaridade,
        'data_nascimento' => $data_nascimento,
        'id' => $usuario_id
    ]);

    echo "<script>
        alert('✅ Perfil atualizado com sucesso!');
        window.location.href = '../dashboard.php';
    </script>";
} catch (PDOException $e) {
    echo "Erro ao atualizar perfil: " . $e->getMessage();
}
