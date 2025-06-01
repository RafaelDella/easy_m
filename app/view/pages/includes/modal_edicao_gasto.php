<div id="modalEdicaoGasto" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalEdicaoGasto')">&times;</span>
        <h2>Editar Gasto</h2>
        <form id="formEdicaoGasto" action="../forms_gasto/4-editar_gasto.php" method="POST">
            <input type="hidden" id="edit_id_gasto" name="id">

            <label for="edit_nome_gasto">Nome do Gasto:</label>
            <input type="text" id="edit_nome_gasto" name="nome_gasto" required>

            <label for="edit_desc_gasto">Descrição:</label>
            <textarea id="edit_desc_gasto" style="padding: 5px; color:white; width: 100%;background-color: #1e1e1e;min-height: 150px;" name="desc_gasto" rows="3" required></textarea>

            <label for="edit_categoria_gasto">Categoria:</label>
            <select id="edit_categoria_gasto" name="categoria_gasto" required>
                <option value="">Selecione uma categoria</option>
                <option value="Alimentação">Alimentação</option>
                <option value="Transporte">Transporte</option>
                <option value="Moradia">Moradia</option>
                <option value="Saúde">Saúde</option>
                <option value="Educação">Educação</option>
                <option value="Lazer">Lazer</option>
                <option value="Contas">Contas</option>
                <option value="Compras">Compras</option>
                <option value="Outro">Outro</option>
            </select>

            <label for="edit_valor_gasto">Valor:</label>
            <input type="number" id="edit_valor_gasto" name="valor_gasto" step="0.01" min="0.01" required>

            <label for="edit_is_imprevisto">Gasto Imprevisto:</label>
            <input type="checkbox" style="width:20px;" id="edit_is_imprevisto" name="is_imprevisto" value="1">

            <label for="edit_data_gasto">Data do Gasto:</label>
            <input type="date" id="edit_data_gasto" name="data_gasto" required>

            
            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" onclick="fecharModal('modalEdicaoGasto')">Cancelar</button>
                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </div>

        </form>
    </div>
</div>