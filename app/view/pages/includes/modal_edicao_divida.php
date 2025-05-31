<div id="modalEditarDivida" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalEditarDivida')">&times;</span>
        <h2>Editar Dívida</h2>
        <form action="../forms_divida/4-editar_divida.php" method="POST" onsubmit="return validarFormularioEdicaoDivida()">
            <input type="hidden" id="edit_id_divida" name="id">

            <label for="edit_nome_divida">Nome:</label>
            <input type="text" id="edit_nome_divida" name="nome_divida" required>

            <label for="edit_valor_total">Valor total (R$):</label>
            <input type="number" id="edit_valor_total" name="valor_total" step="0.01" required>

            <label for="edit_valor_pago">Valor pago (R$):</label>
            <input type="number" id="edit_valor_pago" name="valor_pago" step="0.01" required>

            <label for="edit_taxa_divida">Taxa de juros (%):</label>
            <input type="number" id="edit_taxa_divida" name="taxa_divida" step="0.01" required>

            <label for="edit_categoria_divida">Categoria:</label>
            <select id="edit_categoria_divida" name="categoria_divida" required>
                <option value="">Selecione</option>
                <option value="Cartão">Cartão</option>
                <option value="Empréstimo">Empréstimo</option>
                <option value="Financiamento">Financiamento</option>
                <option value="Outro">Outro</option>
            </select>

            <label for="edit_data_vencimento">Data de vencimento:</label>
            <input type="date" id="edit_data_vencimento" name="data_vencimento" required>

            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" onclick="fecharModal('modalEditarDivida')">Cancelar</button>
                <button type="submit" class="btn-submit">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<script>
    function validarFormularioEdicaoDivida() {
        const nome = document.getElementById('edit_nome_divida').value.trim();
        const valorTotal = parseFloat(document.getElementById('edit_valor_total').value);
        const valorPago = parseFloat(document.getElementById('edit_valor_pago').value);
        const taxa = parseFloat(document.getElementById('edit_taxa_divida').value);
        const categoria = document.getElementById('edit_categoria_divida').value;
        const vencimento = document.getElementById('edit_data_vencimento').value;

        if (!nome || !categoria || !vencimento || isNaN(valorTotal) || valorTotal <= 0 || isNaN(valorPago) || valorPago < 0 || isNaN(taxa) || taxa < 0) {
            alert("❌ Preencha todos os campos corretamente para editar a dívida.");
            return false;
        }
        return true;
    }
</script>