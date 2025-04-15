<?php
require_once '../model/User.php';

class UsuarioController {
    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

            $usuario = new Usuarios();
            if ($usuario->criarUsuario($nome, $email, $senha)) {
                echo "Usuário cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar usuário!";
            }
        }

        // Carregar a view de cadastro
        include '../views/cadastro.php';
    }
}
?>
