<?php
 
    $host = 'localhost';
    $usuario = 'root';
    $senha = '';
    $banco = 'easym';

  
    $conn = new mysqli($host, $usuario, $senha, $banco);

   
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    
    $nome_gasto = $_POST['nome_gasto'];
    $desc_gasto = $_POST['desc_gasto'] ?? null;  
    $categoria_gasto = $_POST['categoria_gasto'];
    $valor_gasto = $_POST['valor_gasto'];
    $data_gasto = $_POST['data_gasto'];
    $is_imprevisto = isset($_POST['is_imprevisto']) ? 1 : 0;


    $usuario_id = 1;

    // session_start();
    // $usuario_id = $_SESSION['usuario_id'];

    
    $sql = "INSERT INTO Gasto (nome_gasto, desc_gasto, categoria_gasto, valor_gasto, is_imprevisto, data_gasto, usuario_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdisi", $nome_gasto, $desc_gasto, $categoria_gasto, $valor_gasto, $is_imprevisto, $data_gasto, $usuario_id);

    
    if ($stmt->execute()) {
        echo "Gasto cadastrado com sucesso!";
    } else {
        echo "Erro ao cadastrar gasto: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
?>
