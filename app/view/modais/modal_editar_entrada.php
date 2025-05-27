<!-- Modal de Edição -->
<div id="modalEditarEntrada" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModalEditar()">&times;</span>
        <h2>Editar Entrada</h2>

        <form action="editar_entrada.php" method="POST" onsubmit="return validarformsEditar()">
            <input type="hidden" name="id" id="editar_entrada_id">

            <label for="editar_descricao">Descrição:</label>
            <input type="text" id="editar_descricao" name="descricao" required>

            <label for="editar_valor">Valor (R$):</label>
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
                <button type="submit" id="btn-submit">Salvar Alterações</button>
                <button type="button" onclick="fecharModalEditar()" class="btn-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>