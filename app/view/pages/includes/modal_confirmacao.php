<?php
// includes/header.php
// Este arquivo contém a estrutura HTML do cabeçalho.
// Não deve conter lógica de sessão ou banco de dados aqui.
// Os caminhos para os assets (imagens) são relativos à página que incluir este header.
?>
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <h3 id="confirmModalTitle">Tem certeza?</h3>
        <p id="confirmModalMessage">Você deseja prosseguir com esta ação?</p>
        <div class="modal-buttons">
            <button class="btn-cancelar" onclick="fecharModal('confirmModal')">Cancelar</button>
            <button class="btn-submit" id="confirmActionButton">Confirmar</button>
        </div>
    </div>
</div>

<script>
    let confirmCallback = null; // Variável para armazenar a função de callback

    function abrirModalConfirmacao(title, message, callback) {
        document.getElementById("confirmModalTitle").innerText = title;
        document.getElementById("confirmModalMessage").innerText = message;
        confirmCallback = callback; // Armazena a função a ser executada na confirmação
        abrirModal('confirmModal');
    }

    document.getElementById("confirmActionButton").addEventListener("click", function() {
        if (confirmCallback) {
            confirmCallback(); // Executa a função armazenada
        }
        fecharModal('confirmModal');
    });
</script>