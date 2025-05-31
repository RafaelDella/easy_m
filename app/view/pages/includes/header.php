<?php
// includes/header.php
// Estrutura do cabeçalho com logo, nome do sistema e ícone de notificações
?>
<header id="top-header">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="logo-container">
        <img src="/easy_m/app/assets/img/logo_2.png" alt="Logo" class="logo-img">
    </div>

    <!-- Ícone e painel de notificações -->
    <div class="notificacao-wrapper">
        <i class="fa-solid fa-clock" id="iconeNotificacao" title="Notificações de vencimento"></i>
        <span class="badge" id="contador-alertas" style="display:none;">0</span>
        <div id="painelNotificacoes" class="painel-notificacoes hidden">
            <h4>Lembretes</h4>
            <ul id="listaNotificacoes"></ul>
        </div>
    </div>

</header>
<script src="/easy_m/app/assets/js/components/notificacoes.js"></script>