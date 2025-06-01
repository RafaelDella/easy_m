<div id="edicaoModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="fecharModalEdicao()">&times;</span>
        <h2>Editar Despesa</h2>
        <form id="formEdicaoDespesa">
            <input type="hidden" id="edit_id_despesa" name="id_despesa">

            <label for="edit_nome_despesa">Nome da Despesa:</label>
            <input type="text" id="edit_nome_despesa" name="nome_despesa" required>

            <label for="edit_descricao_despesa">Descrição:</label>
            <textarea id="edit_descricao_despesa" name="descricao"></textarea>

            <label for="edit_valor_despesa">Valor:</label>
            <input type="number" step="0.01" id="edit_valor_despesa" name="valor_despesa" required>

            <label for="edit_data_vencimento_despesa">Data de Vencimento:</label>
            <input type="date" id="edit_data_vencimento_despesa" name="data_vencimento" required>

            <label for="edit_categoria_despesa">Categoria:</label>
            <select id="edit_categoria_despesa" name="id_categoria" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categoriasDespesa as $cat) : ?>
                    <option value="<?= htmlspecialchars($cat['id_categoria']) ?>"><?= htmlspecialchars($cat['nome_categoria']) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="modal-buttons">
                <button type="button" class="btn red" onclick="fecharModalEdicao()">Cancelar</button>
                <button type="submit" class="btn green">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>