<?php
class DB {
    private $host = 'localhost'; 
    private $dbname = 'easym'; 
    private $user = 'root'; 
    private $password = '';

    private $conn;

    public function connect() {
        if (!$this->conn) {
            try {
                $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Erro na conexão: " . $e->getMessage();
            }
        }
        return $this->conn;
    }
}
?>
