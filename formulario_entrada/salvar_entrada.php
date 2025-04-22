<?php

$host = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'easym';

$conn = new mysqli($host, $usuario, $senha, $banco);


if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}


$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$categoria = $_POST['categoria'];
$data_entrada = $_POST['data_entrada'];

// $_SESSION['usuario_id']
$id_usuario = 1;


$sql = "INSERT INTO Entrada (descricao, valor, categoria, data_entrada, id_usuario) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sdssi", $descricao, $valor, $categoria, $data_entrada, $id_usuario);

if ($stmt->execute()) {
    echo "<script>alert('✅ Entrada registrada com sucesso!'); window.location.href='entrada.html';</script>";
} else {
    echo "Erro ao registrar entrada: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
