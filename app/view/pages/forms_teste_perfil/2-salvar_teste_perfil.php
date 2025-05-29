<?php
// Inicie a sessão para acessar o ID do usuário.
// Adicione esta verificação para evitar o aviso "session already active".
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifique se o usuário está autenticado. Se não estiver, redirecione para a página de login.
if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../forms_login/1-forms_login.html'); // Ajuste o caminho se necessário.
    exit;
}

// Inclua o arquivo de conexão com o banco de dados.
// O caminho abaixo assume que este arquivo está em 'easy_m1/app/view/pages/forms_perfil/'
// e 'db.php' está na raiz do projeto 'easy_m1/'.
require_once __DIR__ . '../../../../db.php';

// Crie uma instância da classe DB e conecte-se ao banco de dados.
$db = new DB();
$pdo = $db->connect();

// Obtenha o ID do usuário da sessão.
$id_usuario = $_SESSION['id_usuario'];

// Verifique se os dados do formulário foram enviados via POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura as respostas do questionário.
    $situacao_atual = $_POST['situacao_atual'] ?? '';
    $preocupacao = $_POST['preocupacao'] ?? '';
    $fim_do_mes = $_POST['fim_do_mes'] ?? '';
    $imprevistos = $_POST['imprevistos'] ?? '';
    $planejamento = $_POST['planejamento'] ?? '';
    $pagamento = $_POST['pagamento'] ?? '';
    $dinheiro_sobrando = $_POST['dinheiro_sobrando'] ?? '';
    $dividas = $_POST['dividas'] ?? '';
    $sentimento_dinheiro = $_POST['sentimento_dinheiro'] ?? '';
    $melhorar = $_POST['melhorar'] ?? '';

    // Inicialize o perfil como 'Doméstico' por padrão.
    $perfil = 'Doméstico';

    // Lógica para determinar o perfil com base nas respostas.
    // O perfil 'Endividado' é priorizado se alguma das condições for verdadeira.
    if (
        $situacao_atual === 'dividas' ||
        $preocupacao === 'quitar_dividas' ||
        $fim_do_mes === 'negativo' ||
        $imprevistos === 'emprestimos' ||
        $dividas === 'tenho_muitas' ||
        $sentimento_dinheiro === 'preocupado' ||
        $melhorar === 'sair_dividas'
    ) {
        $perfil = 'Endividado';
    }
    // Se não for 'Endividado', verifique as condições para 'Poupador'.
    elseif (
        $imprevistos === 'uso_reserva' ||
        $planejamento === 'tento_planejar' ||
        $pagamento === 'em_dia' ||
        $dinheiro_sobrando === 'deixo_parado' ||
        $dividas === 'sem_dividas' ||
        $sentimento_dinheiro === 'tranquilo' ||
        $melhorar === 'alcançar_metas'
    ) {
        $perfil = 'Poupador';
    }
    // Caso contrário, o perfil permanece 'Doméstico'.

    try {
        // Prepare a consulta SQL para atualizar o campo 'perfil' na tabela 'Usuario'.
        $stmt = $pdo->prepare("UPDATE Usuario SET perfil = :perfil WHERE id_usuario = :id_usuario");
        $stmt->execute([
            ':perfil' => $perfil,
            ':id_usuario' => $id_usuario
        ]);

        // Escape as variáveis PHP para uso seguro em JavaScript
        $perfil_js = json_encode($perfil);
        $dashboard_url = json_encode('../dashboard/dashboard.php'); // Verifique se este caminho está correto

        // Gera o script JavaScript para exibir o modal e redirecionar após o fechamento
        echo "<script>";
        echo "window.onload = function() {"; // Garante que o DOM esteja carregado e customAlert esteja disponível
        echo "  if (typeof window.customAlert === 'function') {";
        echo "    window.customAlert('✅ Perfil financeiro definido como ' + " . $perfil_js . " + '!', 'Sucesso!', 'success', function() {";
        echo "      window.location.href = " . $dashboard_url . ";";
        echo "    });";
        echo "  } else {";
        echo "    // Fallback para alert() se customAlert não estiver carregado (improvável com onload)";
        echo "    alert('✅ Perfil financeiro definido como ' + " . $perfil_js . " + '!');";
        echo "    window.location.href = " . $dashboard_url . ";";
        echo "  }";
        echo "};";
        echo "</script>";
        exit; // Termina o script PHP após enviar o JavaScript

    } catch (PDOException $e) {
        // Em caso de erro no banco de dados, exiba a mensagem de erro usando o modal
        $error_message_js = json_encode("Erro ao atualizar perfil: " . $e->getMessage());
        
        echo "<script>";
        echo "window.onload = function() {";
        echo "  if (typeof window.customAlert === 'function') {";
        echo "    window.customAlert(" . $error_message_js . ", 'Erro!', 'error');";
        echo "  } else {";
        echo "    alert(" . $error_message_js . ");";
        echo "  }";
        echo "};";
        echo "</script>";
        exit; // Termina o script PHP após exibir o erro (sem redirecionar automaticamente)
    }
} else {
    // Se o formulário não foi enviado via POST, redirecione para a página do formulário.
    header('Location: 5-forms_teste_perfil.php'); // Ajuste o caminho se necessário.
    exit;
}
?>
