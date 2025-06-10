<div id="modalCadastroMeta" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="fecharModal('modalCadastroMeta')">&times;</span>
        <h2>Cadastrar Nova Meta</h2>
        <form action="../forms_meta/2-cadastrar_meta.php" method="POST">
            <label for="titulo_meta">Nome da Meta:</label>
            <input type="text" id="titulo_meta" name="titulo" required>

            <label for="descricao_meta">Descrição:</label>
            <textarea id="descricao_meta" name="descricao"></textarea>

            <label for="categoria_meta">Categoria:</label>
            <input type="text" id="categoria_meta" name="categoria" required>

            <label for="valor_meta">Valor da Meta:</label>
            <input type="number" step="0.01" id="valor_meta" name="valor_meta" required>

            <label for="previsao_meta">Previsão de Conclusão:</label>
            <input type="date" id="previsao_meta" name="previsao_conclusao" required>

            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" style="margin-top: 15px;" onclick="fecharModal('modalCadastroMeta')">Cancelar</button>
                <button type="submit"style="margin-top: 15px;"  class="btn green">Cadastrar Meta</button>
            </div>
        </form>
    </div>
</div>
