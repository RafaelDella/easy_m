<?php
// includes/header.php
// Este arquivo contém a estrutura HTML do cabeçalho.
// Não deve conter lógica de sessão ou banco de dados aqui.
// Os caminhos para os assets (imagens) são relativos à página que incluir este header.
?>
<div id="modalEditarEntrada" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalEditarEntrada')">&times;</span>
        <h2>Editar Entrada</h2>
        <form action="../forms_entrada/4-editar_entrada.php" method="POST" onsubmit="return validarFormularioEdicaoEntrada()">
            <input type="hidden" id="editar_entrada_id" name="id">

            <label for="editar_descricao">Descrição:</label>
            <input type="text" id="editar_descricao" name="descricao" required>

            <label for="editar_valor">Valor:</label>
            <input type="number" id="editar_valor" name="valor" step="0.01" required>

            <label for="editar_categoria">Categoria:</label>
            <select id="editar_categoria" name="categoria" required>
                <option value="">Selecione</option>
                <option value="Salário">Salário</option>
                <option value="Freelance">Freelance</option>
                <option value="Presente">Presente</option>
                <option value="Outro">Outro</option>
            </select>

            <label for="editar_data_entrada">Data:</label>
            <input type="date" id="editar_data_entrada" name="data_entrada" required>

            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" onclick="fecharModal('modalEditarEntrada')">Cancelar</button>
                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    function editarEntrada(entrada) {
        document.getElementById("editar_entrada_id").value = entrada.id;
        document.getElementById("editar_descricao").value = entrada.descricao;
        document.getElementById("editar_valor").value = entrada.valor;
        document.getElementById("editar_categoria").value = entrada.categoria;
        document.getElementById("editar_data_entrada").value = entrada.data_entrada;

        abrirModal('modalEditarEntrada'); // Usa a função global para abrir o modal
    }

    function validarFormularioEdicaoEntrada() {
        const descricao = document.getElementById('editar_descricao').value.trim();
        const valor = parseFloat(document.getElementById('editar_valor').value);
        const categoria = document.getElementById('editar_categoria').value;
        const data = document.getElementById('editar_data_entrada').value;

        if (!descricao || !categoria || !data || isNaN(valor) || valor <= 0) {
            alert("Por favor, preencha todos os campos corretamente para a edição de entrada!");
            return false;
        }
        return true;
    }
</script>