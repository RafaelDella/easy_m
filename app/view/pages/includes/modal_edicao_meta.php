<div id="modalEdicaoMeta" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="fecharModal('modalEdicaoMeta')">&times;</span>
        <h2>Editar Meta</h2>
        <form id="formEdicaoMeta">
            <input type="hidden" id="editar_id_meta" name="id_meta">

            <label for="editar_titulo_meta">Título:</label>
            <input type="text" id="editar_titulo_meta" name="titulo" required>

            <label for="editar_descricao_meta">Descrição:</label>
            <textarea id="editar_descricao_meta" name="descricao"></textarea>

            <label for="editar_categoria_meta">Categoria:</label>
            <input type="text" id="editar_categoria_meta" name="categoria" required>

            <label for="editar_valor_meta">Valor da Meta:</label>
            <input type="number" step="0.01" id="editar_valor_meta" name="valor_meta" required>

            <label for="editar_previsao_meta">Previsão de Conclusão:</label>
            <input type="date" id="editar_previsao_meta" name="previsao_conclusao" required>

            <div class="modal-buttons">
                <button type="button" class="btn red" onclick="fecharModal('modalEdicaoMeta')">Cancelar</button>
                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>
