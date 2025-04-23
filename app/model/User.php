<?php
require_once '../db.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new DB();
    }

    public function criarUsuario($nome, $email, $senha) {
        $conn = $this->db->connect();
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha);
        return $stmt->execute();
    }

    // Outros métodos de interação com o banco de dados, como listar, buscar, etc.
}
?>
