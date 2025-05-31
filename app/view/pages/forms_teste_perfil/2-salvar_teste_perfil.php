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

        // Saída HTML para mostrar o modal e redirecionar após confirmação
        $perfil_js = json_encode($perfil);
        $redirect_url = json_encode("../dashboard/1-dashboard.php?perfil={$perfil}");

        echo <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
            <head>
            <meta charset="UTF-8">
            <title>Perfil Definido</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="../../../assets/css/components/modal.css">
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    text-align: center;
                }

                .modal-content{
                    justify-content: center;
                    display: flex;
                    flex-direction: column
                }

                .modal-footer{
                    display: flex;
                    justify-content: center;
                }
            </style>
        </head>
        <body>
            <script src="../../../assets/js/components/modal.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (typeof window.customAlert === 'function') {
                        customAlert(
                            'Seu perfil financeiro foi definido como ' + $perfil_js + '!',
                            'Sucesso!',
                            'success',
                            function () {
                                window.location.href = $redirect_url;
                            }
                        );
                    } else {
                        alert('Seu perfil financeiro foi definido como ' + $perfil_js + '!');
                        window.location.href = $redirect_url;
                    }
                });
            </script>
        </body>
        </html>
        HTML;

        exit;
    } catch (PDOException $e) {
        $erro = htmlspecialchars($e->getMessage());
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <title>Erro</title>
            <link rel="stylesheet" href="../../../assets/css/components/modal.css">
        </head>
        <body>
            <script src="../../../assets/js/components/modal.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (typeof window.customAlert === 'function') {
                        customAlert("Erro ao atualizar perfil: {$erro}", "Erro!", "error");
                    } else {
                        alert("Erro ao atualizar perfil: {$erro}");
                    }
                });
            </script>
        </body>
        </html>
        HTML;
        exit;
    }
} else {
    header('Location: 1-forms_teste_perfil.php');
    exit;
}
