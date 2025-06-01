<div id="modalVisualizacaoGasto" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalVisualizacaoGasto')">&times;</span>
        <style>

            #btn_modalVisualizacaoGasto{
                background-color: #0BA18C;
                font-family: Poppins;
                margin-top: 15px;
                color: #fff;
            }
        </style>
        <h2>Detalhes do Gasto</h2>
        <p><strong>Nome:</strong> <span id="view_nome_gasto"></span></p>
        <p><strong>Descrição:</strong> <span id="view_desc_gasto"></span></p>
        <p><strong>Categoria:</strong> <span id="view_categoria_gasto"></span></p>
        <p><strong>Valor:</strong> R$ <span id="view_valor_gasto"></span></p>
        <p><strong>Imprevisto:</strong> <span id="view_is_imprevisto"></span></p>
        <p><strong>Data:</strong> <span id="view_data_gasto"></span></p>
        <button type="button" id="btn_modalVisualizacaoGasto" class="btn gray" onclick="fecharModal('modalVisualizacaoGasto')">Fechar</button>
    </div>
</div>