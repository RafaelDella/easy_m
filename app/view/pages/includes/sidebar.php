<?php
// includes/sidebar.php
// Este arquivo contém a estrutura HTML da barra lateral.
// Ele espera que $nome e $perfilUsuario (ou outras variáveis dinâmicas) já estejam definidas
// na página que o inclui (ex: 1-dashboard.php).

// Se as variáveis não estiverem definidas, podemos usar um valor padrão para evitar erros.
$nome = $nome ?? 'Usuário';
$perfilUsuario = $perfilUsuario ?? 'Não Definido';
?>
<nav id="sidebar">
    <div id="sidebar_content">
        <div id="user">
            <a href="../forms_perfil_usuario/1-forms_perfil_usuario.php" title="Editar Perfil">
                <img src="../../../assets/img/persona.jpg" id="user_avatar" alt="Avatar">
            </a>
            <p id="user_infos">
                <span class="item-description"><?= htmlspecialchars($nome) ?></span>
                <span class="item-description"><?= htmlspecialchars($perfilUsuario) ?></span>
            </p>
        </div>

        <ul id="side_items">
            <li class="side-item">
                <a href="../dashboard/1-dashboard.php">
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="item-description">Dashboard</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../extrato/1-extrato.php">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span class="item-description">Extrato Financeiro</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_entrada/1-forms_entrada.php">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span class="item-description">Gerenciar Entrada</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_gasto/1-forms_gasto.php">
                    <i class="fa-solid fa-sack-xmark"></i>
                    <span class="item-description">Gerenciar Gasto</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_despesa/1-forms_despesa.php">
                    <i class="fa-solid fa-cash-register"></i>
                    <span class="item-description">Gerenciar Despesa</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_divida/1-forms_divida.php">
                    <i class="fa-solid fa-scale-unbalanced"></i>
                    <span class="item-description">Gerenciar Dívida</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_teste_perfil/1-forms_teste_perfil.php">
                    <i class="fa-solid fa-user"></i>
                    <span class="item-description">Teste de Perfil</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../analisar_divida/1-analisar_divida.php">
                    <i class="fa-solid fa-percent"></i>
                    <span class="item-description">Analisar dívida</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_teto_gasto/1-forms_teto_gasto.php">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                    <span class="item-description">Teto de gasto</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_meta/1-forms_meta.php">
                    <i class="fa-solid fa-bullseye"></i>
                    <span class="item-description">Gerenciar Metas</span>
                </a>
            </li>


            <li class="side-item">
                <a href="../calculo_quitacao_divida/1-calculo_quitacao_divida.php">
                    <i class="fa-solid fa-calculator"></i>
                    <span class="item-description">Calculadora de quitação</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../grafico_pizza/1-grafico_pizza.php">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span class="item-description">Gráfico de pizza</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forum/1-forum.php">
                    <i class="fa-solid fa-comments-dollar"></i>
                    <span class="item-description">Fórum</span>
                </a>
            </li>

            <li class="side-item">
                <a href="../forms_login/3-logout_usuario.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="item-description">Logout</span>
                </a>
            </li>

        </ul>

        <button id="open_btn">
            <i id="open_btn_icon" class="fas fa-chevron-right"></i>
        </button>
    </div>
</nav>