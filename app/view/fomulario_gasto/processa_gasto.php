<?php
// ⚙️ CONFIGURAÇÃO DO BANCO
$host = "localhost";
$db = "controle_gastos";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$nome = $_POST['nome_gasto'];
$desc = $_POST['desc_gasto'] ?? null;
$categoria = $_POST['categoria_gasto'];
$valor = $_POST['valor_gasto'];
$data = $_POST['data_gasto'];
$is_imprevisto = isset($_POST['is_imprevisto']) ? 1 : 0;


$usuario_id = 1; 


$sql = "INSERT INTO Gasto 
(nome_gasto, desc_gasto, categoria_gasto, valor_gasto, is_imprevisto, data_gasto, usuario_id)
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssdisi", $nome, $desc, $categoria, $valor, $is_imprevisto, $data, $usuario_id);

if ($stmt->execute()) {
    echo "Gasto cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar gasto: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
