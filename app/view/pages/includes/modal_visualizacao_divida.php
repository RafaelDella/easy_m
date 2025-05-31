<div id="modalVisualizarDivida" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalVisualizarDivida')">&times;</span>
        <h2>Detalhes da Dívida</h2>

        <p><strong>Nome:</strong> <span id="view_nome_divida"></span></p>
        <p><strong>Valor total:</strong> R$ <span id="view_valor_total"></span></p>
        <p><strong>Valor pago:</strong> R$ <span id="view_valor_pago"></span></p>
        <p><strong>Taxa de juros:</strong> <span id="view_taxa_divida"></span>%</p>
        <p><strong>Categoria:</strong> <span id="view_categoria_divida"></span></p>
        <p><strong>Data de vencimento:</strong> <span id="view_data_vencimento"></span></p>

        <div class="modal-buttons" style="justify-content: flex-end;">
            <button type="button" class="btn-cancelar" onclick="fecharModal('modalVisualizarDivida')">Fechar</button>
        </div>
    </div>
</div>

<script>
    function visualizarDivida(idDivida) {
        fetch(`../forms_divida/5-carregar_divida.php?id=${idDivida}`)
            .then(response => {
                if (!response.ok) throw new Error('Erro na resposta do servidor');
                return response.json();
            })
            .then(data => {
                if (data.sucesso) {
                    const d = data.divida;
                    document.getElementById('view_nome_divida').textContent = d.nome_divida;
                    document.getElementById('view_valor_total').textContent = parseFloat(d.valor_total).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    });
                    document.getElementById('view_valor_pago').textContent = parseFloat(d.valor_pago).toLocaleString('pt-BR', {
                        minimumFractionDigits: 2
                    });
                    document.getElementById('view_taxa_divida').textContent = parseFloat(d.taxa_divida).toFixed(2).replace('.', ',');
                    document.getElementById('view_categoria_divida').textContent = d.categoria_divida;
                    document.getElementById('view_data_vencimento').textContent = new Date(d.data_vencimento + 'T00:00:00').toLocaleDateString('pt-BR');

                    abrirModal('modalVisualizarDivida');
                } else {
                    alert('⚠ Erro: ' + (data.erro || 'Dívida não encontrada.'));
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                alert('❌ Erro ao buscar dívida.');
            });
    }
</script>