<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html");
    exit;
}

require_once __DIR__ . '../../../../db.php';

$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario'];

// Validação básica dos dados POST
$descricao = $_POST['descricao'] ?? '';
$valor = filter_var($_POST['valor'] ?? 0, FILTER_VALIDATE_FLOAT);
$categoria = $_POST['categoria'] ?? '';
$data_entrada = $_POST['data_entrada'] ?? '';

// Verificar se os dados obrigatórios estão preenchidos e são válidos
if (empty($descricao) || $valor === false || $valor <= 0 || empty($categoria) || empty($data_entrada)) {
    echo "<script>
        alert('❌ Por favor, preencha todos os campos corretamente para cadastrar a entrada.');
        window.location.href='1-forms_entrada.php'; // Redireciona de volta para a página de formulário
    </script>";
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO Entrada (descricao, valor, categoria, data_entrada, id_usuario)
                           VALUES (:descricao, :valor, :categoria, :data_entrada, :id_usuario)");

    $stmt->execute([
        'descricao' => $descricao,
        'valor' => $valor,
        'categoria' => $categoria,
        'data_entrada' => $data_entrada,
        'id_usuario' => $id_usuario
    ]);

    // Use aspas simples para a string PHP para evitar conflito com aspas internas
    echo '
    <html>
    <head>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <style>
        body {
            font-family: Poppins, sans-serif;
            color: #ffffff;
        }
        .swal2-styled{
            background-color: #0BA18C;
            width: 100px;
        }

        .swal2-show{
            background-color: #;

        }

       .swal2-styled:hover{
            background-color: #0a8171;
        }
    </style>
    <script>
      Swal.fire({
        icon: "success",
        title: "Entrada registrada com sucesso!",
        confirmButtonText: "OK"
      }).then(() => {
        window.location.href = "1-forms_entrada.php";
      });
    </script>
    </body>
    </html>';

    exit;
} catch (PDOException $e) {
    echo "Erro ao registrar entrada: " . $e->getMessage();
}
