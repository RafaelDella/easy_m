<?php
    session_start(); // Inicia a sessão

    // Conexão com o banco
    $conn = new mysqli("localhost", "root", "", "easym");
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Coleta os dados do formulário
    $usuarioOuEmail = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Busca o usuário pelo nome de usuário OU email
    $sql = "SELECT * FROM Usuario WHERE usuario = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuarioOuEmail, $usuarioOuEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Verifica a senha
        if (password_verify($senha, $usuario['senha'])) {
            // Salva informações na sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            // Redireciona para a página principal
            header("Location: pagina_principal.php");
            exit;
        } else {
            echo "Senha incorreta!";
        }
    } else {
        echo "Usuário ou e-mail não encontrado!";
    }

    $stmt->close();
    $conn->close();
?>
