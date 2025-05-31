<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header('Location: ../../forms_login/1-forms_login.html');
    exit;
}

require_once __DIR__ . '../../../../db.php';
$db = new DB();
$pdo = $db->connect();
$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    $perfil = 'Doméstico';

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
    } elseif (
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

    try {
        $stmt = $pdo->prepare("UPDATE Usuario SET perfil = :perfil WHERE id_usuario = :id_usuario");
        $stmt->execute([
            ':perfil' => $perfil,
            ':id_usuario' => $id_usuario
        ]);

        // Redirecionamento direto sem modal
        header("Location: ../dashboard/1-dashboard.php?perfil={$perfil}");
        exit;
    } catch (PDOException $e) {
        echo "Erro ao atualizar perfil: " . $e->getMessage();
        exit;
    }
} else {
    header('Location: 1-forms_teste_perfil.php');
    exit;
}
