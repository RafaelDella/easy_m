<div id="modalCadastroDivida" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalCadastroDivida')">&times;</span>
        <h2>Cadastrar Nova Dívida</h2>
        <form action="../forms_divida/2-processar_cadastro_divida.php" method="POST" onsubmit="return validarFormularioCadastroDivida()">
            <label for="nome_divida">Nome da dívida:</label>
            <input type="text" id="nome_divida" name="nome_divida" required>

            <label for="valor_total">Valor total (R$):</label>
            <input type="number" id="valor_total" name="valor_total" step="0.01" required>

            <label for="valor_pago">Valor pago (R$):</label>
            <input type="number" id="valor_pago" name="valor_pago" step="0.01" required>

            <label for="taxa_divida">Taxa de juros (% ao mês):</label>
            <input type="number" id="taxa_divida" name="taxa_divida" step="0.01" required>

            <label for="categoria_divida">Categoria:</label>
            <select id="categoria_divida" name="categoria_divida" required>
                <option value="">Selecione</option>
                <option value="Cartão">Cartão</option>
                <option value="Empréstimo">Empréstimo</option>
                <option value="Financiamento">Financiamento</option>
                <option value="Outro">Outro</option>
            </select>

            <label for="data_vencimento">Data de vencimento:</label>
            <input type="date" id="data_vencimento" name="data_vencimento" required>

            <div class="modal-buttons">
                <button type="button" class="btn-cancelar" onclick="fecharModal('modalCadastroDivida')">Cancelar</button>
                <button type="submit" class="btn-submit">Cadastrar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function validarFormularioCadastroDivida() {
        const nome = document.getElementById('nome_divida').value.trim();
        const valorTotal = parseFloat(document.getElementById('valor_total').value);
        const valorPago = parseFloat(document.getElementById('valor_pago').value);
        const taxa = parseFloat(document.getElementById('taxa_divida').value);
        const categoria = document.getElementById('categoria_divida').value;
        const vencimento = document.getElementById('data_vencimento').value;

        if (!nome || !categoria || !vencimento || isNaN(valorTotal) || isNaN(valorPago) || isNaN(taxa) || valorTotal <= 0 || taxa < 0) {
            alert("Preencha todos os campos corretamente para cadastrar a dívida.");
            return false;
        }
        return true;
    }
</script>