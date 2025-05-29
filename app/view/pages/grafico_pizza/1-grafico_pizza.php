<?php
// Este arquivo é a página do formulário de Perfil Financeiro.
// Ele inclui a sidebar e o header, e contém a lógica do formulário.

// 1. Inicie a sessão se você estiver usando sessões para autenticação ou dados do usuário.
session_start();

// 2. Lógica de verificação de autenticação.
// Se o usuário não estiver autenticado, redirecione para a página de login.
// O caminho deve ser ajustado conforme a localização do seu arquivo de login
// em relação a este arquivo (perfil_financeiro.php).
// Exemplo: se perfil_financeiro.php está em 'easy_m1/app/view/pages/forms_perfil/'
// e o login está em 'easy_m1/forms_login/1-forms_login.html', o caminho seria '../../../../forms_login/1-forms_login.html'.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html"); // Ajuste este caminho se necessário!
    exit;
}

// 3. Inclua a conexão com o banco de dados.
// O caminho abaixo assume que este arquivo está em 'easy_m1/app/view/pages/forms_perfil/'
// e 'db.php' está na raiz do projeto 'easy_m1/'.
require_once __DIR__ . '../../../../db.php';

// Crie uma instância da classe DB e conecte-se ao banco de dados.
$db = new DB();
$pdo = $db->connect();

// Obtenha o ID do usuário da sessão.
$id_usuario = $_SESSION['id_usuario'];

// 4. Obtenha os dados do usuário (necessários para a sidebar e header).
// Busca o nome e perfil do usuário da tabela 'Usuario'.
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

// Defina as variáveis $nome e $perfilUsuario com os dados do banco de dados,
// ou use valores de fallback se os dados não forem encontrados.
$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Nenhuma lógica de dados específica do dashboard é necessária aqui,
// pois esta é a página do formulário de perfil.
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Financeiro - easy_m</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/5-forms_perfil.css">
</head>
    <body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>
        <main>

        </main>
        <script src="../../../assets/js/components/sidebar.js"></script>
    </body>

</html>