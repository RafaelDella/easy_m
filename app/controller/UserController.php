<?php
require_once '../model/User.php';

class UsuarioController {
    public function cadastrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nome = $_POST['nome'];
            $usuario = $_POST['usuario'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            $cpf = $_POST['cpf'];
            $escolaridade = $_POST['escolaridade'];
            $perfil = $_POST['perfil'] ?? null;
            $data_nascimento = $_POST['data_nascimento'];

            $usuarioModel = new User();
            $sucesso = $usuarioModel->criarUsuario(
                $nome,
                $usuario,
                $email,
                $senha, // será criptografada dentro do método
                $cpf,
                $escolaridade,
                $perfil,
                $data_nascimento
            );

            if ($sucesso) {
                echo "Usuário cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar usuário!";
            }
        }

        // Carregar a view de cadastro (formulário HTML)
        include '../views/cadastro.php';
    }
}
?>
<!-- ALTER TABLE Clientes
ADD COLUMN email VARCHAR(100) NOT NULL; -->