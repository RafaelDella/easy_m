<?php
// Este arquivo é a página do formulário de Perfil Financeiro.
// Ele inclui a sidebar e o header, e contém a lógica do formulário.

// 1. Inicie a sessão se você estiver usando sessões para autenticação ou dados do usuário.
session_start();

// 2. Lógica de verificação de autenticação.
// Se o usuário não estiver autenticado, redirecione para a página de login.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../forms_login/1-forms_login.html"); // Ajuste este caminho se necessário!
    exit;
}

// 3. Inclua a conexão com o banco de dados.
require_once __DIR__ . '../../../../db.php';

// Crie uma instância da classe DB e conecte-se ao banco de dados.
$db = new DB();
$pdo = $db->connect();

// Obtenha o ID do usuário da sessão.
$id_usuario = $_SESSION['id_usuario'];

// 4. Obtenha os dados do usuário (necessários para a sidebar e header).
$stmtUsuario = $pdo->prepare("SELECT nome, perfil FROM Usuario WHERE id_usuario = :id_usuario");
$stmtUsuario->execute([':id_usuario' => $id_usuario]);
$dadosUsuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome = $dadosUsuario['nome'] ?? 'Usuário';
$perfilUsuario = $dadosUsuario['perfil'] ?? 'Não definido';

// Nenhuma lógica de dados específica do dashboard é necessária aqui,
// pois esta é a página do formulário de perfil.
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil Financeiro - easy_m</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../../assets/css/components/header.css">
    <link rel="stylesheet" href="../../../assets/css/components/sidebar.css">
    <link rel="stylesheet" href="../../../assets/css/components/modal.css">
    <link rel="stylesheet" href="../../../assets/css/pages/5-forms_teste_perfil.css">
</head>

<body>
    <?php include_once('../includes/sidebar.php'); ?>
    <?php include_once('../includes/header.php'); ?>

    <main>
        <h1>Perfil Financeiro - easy_m</h1>
        <form id="formulario" action="2-salvar_teste_perfil.php" method="POST"> 
            <div class="form-step active">
                <p>1.1 Como você descreveria sua situação financeira atualmente?</p>
                <label><input type="radio" name="situacao_atual" value="dividas" required> Tenho muitas dívidas e dificuldades
                    para pagar as contas.</label>
                <label><input type="radio" name="situacao_atual" value="sem_economizar"> Consigo pagar as contas, mas não
                    consigo
                    economizar.</label>
                <label><input type="radio" name="situacao_atual" value="desorganizado"> Pago as contas, mas não consigo me
                    organizar.</label>
            </div>
            <div class="form-step">
                <p>1.2 Qual é a sua principal preocupação financeira no momento?</p>
                <label><input type="radio" name="preocupacao" value="quitar_dividas" required> Quitar dívidas.</label>
                <label><input type="radio" name="preocupacao" value="organizar_gastos"> Organizar melhor os gastos do dia a
                    dia.</label>
                <label><input type="radio" name="preocupacao" value="compreender_gastos"> Compreender onde meu dinheiro é
                    gasto.</label>
            </div>
            <div class="form-step">
                <p>1.3 O que acontece com seu dinheiro no fim do mês?</p>
                <label><input type="radio" name="fim_do_mes" value="negativo" required> Fico no negativo ou preciso parcelar
                    despesas.</label>
                <label><input type="radio" name="fim_do_mes" value="sem_sobra"> Consigo pagar tudo, mas não sobra nada.</label>
                <label><input type="radio" name="fim_do_mes" value="as_vezes_falta"> Às vezes sobra, às vezes falta.</label>
            </div>
            <div class="form-step">
                <p>1.4 Como você costuma lidar com imprevistos financeiros?</p>
                <label><input type="radio" name="imprevistos" value="emprestimos" required> Preciso pegar empréstimos ou uso o
                    limite do banco.</label>
                <label><input type="radio" name="imprevistos" value="cobre_apertado"> Consigo cobrir pequenos imprevistos, mas
                    fico apertado.</label>
                <label><input type="radio" name="imprevistos" value="uso_reserva"> Tenho uma reserva, mas todo mês acabo usando
                    ela.</label>
            </div>
            <div class="form-step">
                <p>1.5 Você faz algum tipo de planejamento financeiro?</p>
                <label><input type="radio" name="planejamento" value="nao_planejo" required> Não, pago as contas conforme elas
                    chegam.</label>
                <label><input type="radio" name="planejamento" value="planejo_sobra_pouco"> Sim, mas depois não sobra quase
                    nada.</label>
                <label><input type="radio" name="planejamento" value="tento_planejar"> Sim, tento organizar, mas nem sempre
                    consigo seguir.</label>
            </div>
            <div class="form-step">
                <p>2.1 Como você costuma pagar suas contas?</p>
                <label><input type="radio" name="pagamento" value="atraso_contas" required> Atraso algumas contas porque falta
                    dinheiro.</label>
                <label><input type="radio" name="pagamento" value="em_dia_com_sufoco"> Pago tudo em dia, mas com sufoco.</label>
                <label><input type="radio" name="pagamento" value="em_dia"> Pago tudo em dia.</label>
            </div>
            <div class="form-step">
                <p>2.2 O que você faz quando sobra dinheiro no fim do mês?</p>
                <label><input type="radio" name="dinheiro_sobrando" value="pagar_dividas" required> Uso para pagar contas
                    atrasadas ou outras despesas.</label>
                <label><input type="radio" name="dinheiro_sobrando" value="compartilhar"> Utilizo com alguém que amo.</label>
                <label><input type="radio" name="dinheiro_sobrando" value="deixo_parado"> Deixo parado na conta.</label>
            </div>
            <div class="form-step">
                <p>2.3 Você tem dívidas atualmente?</p>
                <label><input type="radio" name="dividas" value="tenho_muitas" required> Sim, e não sei como vou pagar.</label>
                <label><input type="radio" name="dividas" value="tenho_controladas"> Sim, mas estou conseguindo pagar aos
                    poucos.</label>
                <label><input type="radio" name="dividas" value="sem_dividas"> Não tenho dívidas.</label>
            </div>
            <div class="form-step">
                <p>2.4 Como você se sente em relação ao seu dinheiro?</p>
                <label><input type="radio" name="sentimento_dinheiro" value="preocupado" required> Preocupado(a), porque não
                    consigo controlar meus gastos.</label>
                <label><input type="radio" name="sentimento_dinheiro" value="neutro"> Neutro(a), consigo me virar, mas sem
                    sobras.</label>
                <label><input type="radio" name="sentimento_dinheiro" value="tranquilo"> Tranquilo(a), pois sei exatamente onde
                    está meu dinheiro.</label>
            </div>
            <div class="form-step">
                <p>2.5 O que você gostaria de melhorar na sua vida financeira?</p>
                <label><input type="radio" name="melhorar" value="sair_dividas" required> Sair das dívidas.</label>
                <label><input type="radio" name="melhorar" value="organizar_gastos"> Organizar melhor meus gastos.</label>
                <label><input type="radio" name="melhorar" value="alcançar_metas" required> Utilizar meu dinheiro para alcançar minhas
                    metas.</label>
            </div>
            <div class="button-wrapper">
                <button type="button" id="prevBtn">Voltar</button>
                <button type="button" id="nextBtn">Próximo</button>
                <button type="submit" id="submitBtn" style="display: none;">Enviar</button>
            </div>
        </form>
    </main>                    
    <script src="../../../assets/js/components/sidebar.js"></script>
    <script src="../../../assets/js/components/modal.js"></script>
    <script src="../../../assets/js/pages/3-forms_teste_perfil.js"></script>
</body>

</html>

