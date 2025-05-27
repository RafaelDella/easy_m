<!-- Modal de Cadastro -->
<div id="modalEntrada" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal()">&times;</span>
        <h2>Nova Entrada</h2>

        <form action="salvar_entrada.php" method="POST" onsubmit="return validarforms()">
            <input type="hidden" name="id" id="entrada_id">

            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" required>

            <label for="valor">Valor (R$):</label>
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
                <button type="submit" id="btn-submit">Salvar Entrada</button>
                <button type="button" onclick="fecharModal()" class="btn-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</div>




