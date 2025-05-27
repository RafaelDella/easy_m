<?php
// includes/sidebar.php
// Este arquivo contém a estrutura HTML da barra lateral.
// Ele espera que $nome e $perfilUsuario (ou outras variáveis dinâmicas) já estejam definidas
// na página que o inclui (ex: dashboard.php).

// Se as variáveis não estiverem definidas, podemos usar um valor padrão para evitar erros.
$nome = $nome ?? 'Usuário';
$perfilUsuario = $perfilUsuario ?? 'Não Definido';
?>
<nav id="sidebar">
    <div id="sidebar_content">
        <div id="user">
            <a href="perfil_usuario/perfil_usuario.html" title="Editar Perfil">
                <img src="assets/image/zeca.jpg" id="user_avatar" alt="Avatar">
            </a>
            <p id="user_infos">
                <span class="item-description"><?= htmlspecialchars($nome) ?></span>
                <span class="item-description"><?= htmlspecialchars($perfilUsuario) ?></span>
            </p>
        </div>

        <ul id="side_items">
            <li class="side-item active">
                <a href="dashboard.php">
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="item-description">Painel</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/extrato_page/extrato_view.php">
                    <i class="fa-solid fa-file-invoice"></i>
                    <span class="item-description">Extrato</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/form_entrada/formulario_entrada.php">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span class="item-description">Nova Entrada</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulario_gasto/forms_gasto.html">
                    <i class="fa-solid fa-sack-xmark"></i>
                    <span class="item-description">Novo Gasto</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulario_divida/index.php">
                    <i class="fa-solid fa-cash-register"></i>
                    <span class="item-description">Nova dívida</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulário_perfil/forms_perfil.html">
                    <i class="fa-solid fa-user"></i>
                    <span class="item-description">Teste de Perfil</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulário_perfil/forms_perfil.html">
                    <i class="fa-solid fa-user"></i>
                    <span class="item-description">Analisar dívida</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulário_perfil/forms_perfil.html">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                    <span class="item-description">Teto de gasto</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/calculo_quitacao_divida/calcular_quitacao.html">
                    <i class="fa-solid fa-calculator"></i>
                    <span class="item-description">Calculadora de quitação</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulário_perfil/forms_perfil.html">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span class="item-description">Gráfico de pizza</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulário_perfil/forms_perfil.html">
                    <i class="fa-solid fa-comments-dollar"></i>
                    <span class="item-description">Fórum</span>
                </a>
            </li>

            <li class="side-item">
                <a href="view/formulario_login/form_login.html">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="item-description">Logout</span>
                </a>
            </li>
        </ul>

        <button id="open_btn">
            <i id="open_btn_icon" class="fa-solid fa-chevron-right"></i>
        </button>
    </div>
</nav>