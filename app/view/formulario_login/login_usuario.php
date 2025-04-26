<?php
    session_start();

    $conn = new mysqli("localhost", "root", "", "easym");
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    $usuarioOuEmail = $_POST['usuario'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM Usuario WHERE usuario = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $usuarioOuEmail, $usuarioOuEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

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
