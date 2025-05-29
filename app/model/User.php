<?php
require_once '../db.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function criarUsuario($nome, $usuario, $email, $senha, $cpf, $escolaridade, $perfil, $data_nascimento) {
        $conn = $this->db->connect();

        // Criptografar a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Usuario 
                    (nome, usuario, email, senha, cpf, escolaridade, perfil, data_nascimento) 
                VALUES 
                    (:nome, :usuario, :email, :senha, :cpf, :escolaridade, :perfil, :data_nascimento)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':cpf', $cpf);
        $stmt->bindParam(':escolaridade', $escolaridade);
        $stmt->bindParam(':perfil', $perfil);
        $stmt->bindParam(':data_nascimento', $data_nascimento);

        return $stmt->execute();
    }

    // Exemplo de método extra
    public function buscarUsuarioPorEmail($email) {
        $conn = $this->db->connect();
        $sql = "SELECT * FROM Usuario WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
