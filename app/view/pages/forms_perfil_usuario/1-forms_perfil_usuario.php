<?php
session_start();

// 1. Verificação de autenticação
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

// 2. Conexão com o banco de dados
require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();

$id_usuario = $_SESSION['id_usuario'];

// 3. Obtenção de dados do usuário (para header e sidebar)
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <title>Meu Perfil - EasyM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/15-forms_perfil_usuario.css">
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>
        <div class="form-container">
            <h2>Meu Perfil</h2>

            <form id="formPerfil" action="3-atualizar_perfil_usuario.php" method="POST">
                <input type="hidden" id="usuario_id" name="usuario_id">

                <label for="nome">Nome completo:</label>
                <input type="text" id="nome" name="nome" required>

                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>

                <label for="usuario">Nome de usuário:</label>
                <input type="text" id="usuario" name="usuario" required>

                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" maxlength="14" required>

                <label for="escolaridade">Escolaridade:</label>
                <input type="text" id="escolaridade" name="escolaridade" required>

                <label for="data_nascimento">Data de nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required>

                <h4 id="titulo-secao" style="font-weight: normal;">Redefinir Senha</h4>

                <label for="senha_atual">Senha atual:</label>
                <input type="password" style="background-color: #2B2B2B;" id="senha_atual" name="senha_atual">

                <label for="nova_senha">Nova senha:</label>
                <input type="password" style="background-color: #2B2B2B;" id="nova_senha" name="nova_senha">

                <label for="confirmar_senha">Confirmar nova senha:</label>
                <input type="password" style="background-color: #2B2B2B;" id="confirmar_senha" name="confirmar_senha">


                <button type="submit">Salvar Alterações</button>
                <button type="button" class="btn-excluir-conta" onclick="confirmarExclusao()">Excluir Conta</button>
                <a href="../dashboard/1-dashboard.php" class="voltar-link">← Voltar para o Painel</a>
            </form>
        </div>
    </main>

    <script src="../../../assets/js/components/sidebar.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", async () => {
            try {
                const resposta = await fetch("2-carregar_perfil_usuario.php");
                const dados = await resposta.json();

                if (dados.sucesso) {
                    const u = dados.usuario;
                    document.getElementById("usuario_id").value = u.id;
                    document.getElementById("nome").value = u.nome;
                    document.getElementById("email").value = u.email;
                    document.getElementById("usuario").value = u.usuario;
                    document.getElementById("cpf").value = u.cpf;
                    document.getElementById("escolaridade").value = u.escolaridade;
                    document.getElementById("data_nascimento").value = u.data_nascimento;
                } else {
                    alert("❌ Não foi possível carregar os dados do perfil.");
                }
            } catch (err) {
                alert("❌ Erro ao carregar dados do perfil.");
                console.error(err);
            }
        });

        function confirmarExclusao() {
            if (confirm("⚠️ Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.")) {
                window.location.href = "4-excluir_perfil_usuario.php";
            }
        }
    </script>
</body>

</html>