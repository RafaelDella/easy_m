<div id="modalCadastroGasto" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalCadastroGasto')">&times;</span>
        <h2>Cadastrar Novo Gasto</h2>
        <form action="../forms_gasto/2-processar_cadastro_gasto.php" method="POST">
            <label for="nome_gasto_cadastro">Nome do Gasto:</label>
            <input type="text" id="nome_gasto_cadastro" name="nome_gasto" required>

            <label for="desc_gasto_cadastro">Descrição:</label>
            <textarea id="desc_gasto_cadastro" style="padding: 5px; color:white; width: 100%;background-color: #1e1e1e;min-height: 150px;" name="desc_gasto" rows="3" required></textarea>

            <label for="categoria_gasto_cadastro">Categoria:</label>
            <select id="categoria_gasto_cadastro" name="categoria_gasto" required>
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

            <label for="valor_gasto_cadastro">Valor:</label>
            <input type="number" id="valor_gasto_cadastro" name="valor_gasto" step="0.01" min="0.01" required>

            <label for="is_imprevisto_cadastro">Gasto Imprevisto:</label>
            <input type="checkbox" style="width:20px" id="is_imprevisto_cadastro" name="is_imprevisto" value="1">

            <label for="data_gasto_cadastro">Data do Gasto:</label>
            <input type="date" id="data_gasto_cadastro" name="data_gasto" required>

            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" style="margin-top: 15px;" onclick="fecharModal('modalCadastroGasto')">Cancelar</button>
                <button type="submit" style="margin-top: 15px;" class="btn green">Cadastrar Gasto</button>
            </div>
        </form>
    </div>
</div>