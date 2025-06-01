<div id="cadastroModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="fecharModal()">&times;</span>
        <h2>Cadastrar Nova Despesa</h2>
        <form id="formCadastroDespesa">
            <label for="nome_despesa">Nome da Despesa:</label>
            <input type="text" id="nome_despesa" name="nome_despesa" required>

            <label for="descricao_despesa">Descrição:</label>
            <textarea id="descricao_despesa" name="descricao"></textarea>

            <label for="valor_despesa">Valor:</label>
            <input type="number" step="0.01" id="valor_despesa" name="valor_despesa" required>

            <label for="data_vencimento_despesa">Data de Vencimento:</label>
            <input type="date" id="data_vencimento_despesa" name="data_vencimento" required>

            <label for="categoria_despesa">Categoria:</label>
            <select id="categoria_despesa" name="id_categoria" required>
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categoriasDespesa as $cat) : ?>
                    <option value="<?= htmlspecialchars($cat['id_categoria']) ?>"><?= htmlspecialchars($cat['nome_categoria']) ?></option>
                <?php endforeach; ?>
            </select>

            <div class="modal-buttons">
                <button type="button" class="btn red" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn green">Cadastrar</button>
            </div>
        </form>
    </div>
</div>