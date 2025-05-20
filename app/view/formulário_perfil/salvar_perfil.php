<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../formularioulario_login/formulario_login.html');
    exit;
}

$db = new DB();
$pdo = $db->connect();

$usuario_id = $_SESSION['usuario_id'];

// Captura as respostas do questionário
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

// Definir perfil baseado nas respostas
$perfil = 'Doméstico'; // valor padrão

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
    $situacao_atual === 'sem_economizar' ||
    $situacao_atual === 'desorganizado' ||
    $preocupacao === 'organizar_gastos' ||
    $preocupacao === 'compreender_gastos' ||
    $fim_do_mes === 'sem_sobra' ||
    $fim_do_mes === 'as_vezes_falta' ||
    $imprevistos === 'cobre_apertado' ||
    $planejamento === 'planejo_sobra_pouco' ||
    $pagamento === 'em_dia_com_sufoco' ||
    $melhorar === 'organizar_gastos'
) {
    $perfil = 'Doméstico';
} elseif (
    $situacao_atual === 'desorganizado' ||
    $preocupacao === 'alcançar_metas' ||
    $fim_do_mes === 'as_vezes_falta' ||
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
    // Atualizar o campo 'perfil' no banco
    $stmt = $pdo->prepare("UPDATE Usuario SET perfil = :perfil WHERE id = :usuario_id");
    $stmt->execute([
        ':perfil' => $perfil,
        ':usuario_id' => $usuario_id
    ]);

    echo "<script>alert('✅ Perfil financeiro definido como \"$perfil\"!'); window.location.href='../dashboard.php';</script>";
} catch (PDOException $e) {
    echo "Erro ao atualizar perfil: " . $e->getMessage();
}
?>
