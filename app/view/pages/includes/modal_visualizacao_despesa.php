<div id="visualizacaoModal" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="fecharModalVisualizacao()">&times;</span>
        <h2>Detalhes da Despesa</h2>
        <div id="detalhesDespesa">
            <p><strong>Nome:</strong> <span id="view_nome_despesa"></span></p>
            <p><strong>Descrição:</strong> <span id="view_descricao_despesa"></span></p>
            <p><strong>Valor:</strong> R$ <span id="view_valor_despesa"></span></p>
            <p><strong>Data de Vencimento:</strong> <span id="view_data_vencimento_despesa"></span></p>
            <p><strong>Categoria:</strong> <span id="view_nome_categoria"></span></p>
        </div>
        <div class="modal-buttons">
            <button type="button" class="btn blue" onclick="fecharModalVisualizacao()">Fechar</button>
        </div>
    </div>
</div>