document.addEventListener("DOMContentLoaded", function() {

    /**
     * Função para preencher e abrir o modal de edição de Gasto.
     * Recebe um objeto com os dados do gasto a ser editado.
     * @param {Object} gastoData - Objeto contendo os dados do gasto (id, nome_gasto, desc_gasto, etc.).
     */
    window.editarGasto = function(gastoData) {
        // Preenche os campos do formulário de edição com os dados do gasto
        document.getElementById('edit_id_gasto').value = gastoData.id;
        document.getElementById('edit_nome_gasto').value = gastoData.nome_gasto;
        document.getElementById('edit_desc_gasto').value = gastoData.desc_gasto;
        document.getElementById('edit_categoria_gasto').value = gastoData.categoria_gasto;
        document.getElementById('edit_valor_gasto').value = gastoData.valor_gasto;
        
        // O checkbox 'is_imprevisto' deve ser marcado se o valor for 1
        document.getElementById('edit_is_imprevisto').checked = gastoData.is_imprevisto == 1; 
        
        document.getElementById('edit_data_gasto').value = gastoData.data_gasto; // A data já deve vir no formato 'YYYY-MM-DD'

        // Abre o modal de edição
        abrirModal('modalEdicaoGasto');
    };

    /**
     * Função para buscar e preencher o modal de visualização de Gasto.
     * Faz uma requisição AJAX para obter os detalhes do gasto pelo ID.
     * @param {number} idGasto - O ID do gasto a ser visualizado.
     */
    window.visualizarGasto = function(idGasto) {
        // Requisição AJAX para buscar os dados do gasto
        fetch(`../../../view/pages/forms_gasto/5-carregar_entrda.php?id=${idGasto}`)
            .then(response => {
                // Verifica se a resposta foi bem-sucedida (status 200 OK)
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.sucesso) {
                    const gasto = data.gasto;
                    // Preenche os campos do modal de visualização
                    document.getElementById('view_nome_gasto').textContent = gasto.nome_gasto;
                    document.getElementById('view_desc_gasto').textContent = gasto.desc_gasto;
                    document.getElementById('view_categoria_gasto').textContent = gasto.categoria_gasto;
                    
                    // Formata o valor para exibição em moeda brasileira
                    document.getElementById('view_valor_gasto').textContent = parseFloat(gasto.valor_gasto).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    
                    document.getElementById('view_is_imprevisto').textContent = gasto.is_imprevisto == 1 ? 'Sim' : 'Não';
                    
                    // Formata a data para exibição no formato brasileiro
                    document.getElementById('view_data_gasto').textContent = new Date(gasto.data_gasto + 'T00:00:00').toLocaleDateString('pt-BR');

                    // Abre o modal de visualização
                    abrirModal('modalVisualizacaoGasto');
                } else {
                    // Exibe mensagem de erro se a busca não foi bem-sucedida
                    alert('Erro ao buscar detalhes do gasto: ' + (data.mensagem || 'Gasto não encontrado.'));
                }
            })
            .catch(error => {
                // Captura e loga erros da requisição AJAX
                console.error('Erro na requisição AJAX para visualizarGasto:', error);
                alert('Erro ao buscar detalhes do gasto. Por favor, tente novamente.');
            });
    };
});