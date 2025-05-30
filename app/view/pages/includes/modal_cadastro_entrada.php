<?php
// includes/header.php
// Este arquivo contém a estrutura HTML do cabeçalho.
// Não deve conter lógica de sessão ou banco de dados aqui.
// Os caminhos para os assets (imagens) são relativos à página que incluir este header.
?>
<div id="modalCadastroEntrada" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalCadastroEntrada')">&times;</span>
        <h2>Cadastrar Nova Entrada</h2>
        <form action="../forms_entrada/2-processar_cadastro_entrada.php" method="POST" onsubmit="return validarFormularioCadastroEntrada()">
            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" required>

            <label for="valor">Valor:</label>
            <input type="number" id="valor" name="valor" step="0.01" required>

            <label for="categoria">Categoria:</label>
            <select id="categoria" name="categoria" required>
                <option value="">Selecione</option>
                <option value="Salário">Salário</option>
                <option value="Freelance">Freelance</option>
                <option value="Presente">Presente</option>
                <option value="Outro">Outro</option>
            </select>

            <label for="data_entrada">Data:</label>
            <input type="date" id="data_entrada" name="data_entrada" required>

            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" onclick="fecharModal('modalCadastroEntrada')">Cancelar</button>
                <button type="submit" class="btn-submit">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function validarFormularioCadastroEntrada() {
        const descricao = document.getElementById('descricao').value.trim();
        const valor = parseFloat(document.getElementById('valor').value);
        const categoria = document.getElementById('categoria').value;
        const data = document.getElementById('data_entrada').value;

        if (!descricao || !categoria || !data || isNaN(valor) || valor <= 0) {
            alert("Por favor, preencha todos os campos corretamente para o cadastro de entrada!");
            return false;
        }
        return true;
    }
</script>