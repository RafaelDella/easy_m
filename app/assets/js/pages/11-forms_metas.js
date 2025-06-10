document.addEventListener("DOMContentLoaded", function () {

    /**
     * Função para buscar e preencher o modal de visualização de Meta.
     * Faz uma requisição AJAX para obter os detalhes da meta pelo ID.
     * @param {number} idMeta - O ID da meta a ser visualizada.
     */
    window.visualizarMeta = function (idMeta) {
        // Abre o modal de visualização imediatamente
        abrirModal('modalVisualizacaoMeta');

        // Define o estado de carregamento nos campos
        document.getElementById('view_titulo_meta').textContent = 'Carregando...';
        document.getElementById('view_descricao_meta').textContent = 'Carregando...';
        document.getElementById('view_categoria_meta').textContent = 'Carregando...';
        document.getElementById('view_valor_meta').textContent = 'Carregando...';
        document.getElementById('view_previsao_meta').textContent = 'Carregando...';

        // Faz a requisição AJAX para buscar os detalhes da meta
        fetch(`../../../view/pages/forms_meta/4-carregar_meta.php?id=${idMeta}`)
            .then(response => {
                if (!response.ok) {
                    // Tenta ler a resposta como texto para ver a mensagem de erro do servidor
                    return response.text().then(text => {
                        throw new Error(`Erro na resposta do servidor: ${response.status} - ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.sucesso && data.meta) {
                    const meta = data.meta;
                    // Preenche o modal com os dados da meta
                    document.getElementById('view_titulo_meta').textContent = meta.titulo;
                    document.getElementById('view_descricao_meta').textContent = meta.descricao;
                    document.getElementById('view_categoria_meta').textContent = meta.categoria;
                    document.getElementById('view_valor_meta').textContent = parseFloat(meta.valor_meta).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    // Adiciona 'T00:00:00' para evitar problemas com fuso horário ao converter a data
                    document.getElementById('view_previsao_meta').textContent = new Date(meta.previsao_conclusao + 'T00:00:00').toLocaleDateString('pt-BR');
                } else {
                    console.error('Erro no servidor ou meta não encontrada:', data.erro || 'Resposta sem sucesso.');
                    alert('Erro ao carregar detalhes da meta.');
                    fecharModal('modalVisualizacaoMeta');
                }
            })
            .catch(error => {
                console.error('Erro na requisição AJAX para visualizarMeta:', error);
                alert('Não foi possível carregar os detalhes da meta. Verifique o console para mais informações.');
                fecharModal('modalVisualizacaoMeta');
            });
    };

    /**
     * Função para preencher e abrir o modal de edição de Meta.
     * Recebe um objeto com os dados da meta a ser editada.
     * @param {Object} metaData - Objeto contendo os dados da meta.
     */
    window.editarMeta = function (metaData) {
        // Preenche os campos do formulário de edição com os dados da meta
        document.getElementById('editar_id_meta').value = metaData.id;
        document.getElementById('editar_titulo_meta').value = metaData.titulo;
        document.getElementById('editar_descricao_meta').value = metaData.descricao;
        document.getElementById('editar_categoria_meta').value = metaData.categoria;
        document.getElementById('editar_valor_meta').value = metaData.valor_meta;
        document.getElementById('editar_previsao_meta').value = metaData.previsao_conclusao; // O input type="date" espera o formato YYYY-MM-DD

        // Abre o modal de edição
        abrirModal('modalEdicaoMeta');
    };

    // --- Listener para SUBMISSÃO do formulário de edição ---
    // Adaptado para funcionar com um backend PHP que retorna uma página HTML de alerta.
    const formEdicaoMeta = document.getElementById('formEdicaoMeta');

    if (formEdicaoMeta) {
        formEdicaoMeta.addEventListener('submit', async (event) => {
            event.preventDefault(); // Impede o envio padrão para podermos usar fetch

            const formData = new FormData(formEdicaoMeta);

            try {
                const response = await fetch('../../../view/pages/forms_meta/5-atualizar_meta.php', {
                    method: 'POST',
                    body: formData
                });

                // Se a requisição foi bem-sucedida (status 200-299),
                // o PHP retornou uma página HTML de alerta.
                if (response.ok) {
                    // Lemos a resposta como texto (HTML) e substituímos o conteúdo da página atual por ela.
                    const htmlResposta = await response.text();
                    document.documentElement.innerHTML = htmlResposta;
                } else {
                    // Se houver um erro de servidor (ex: 404, 500), lançamos um erro.
                    throw new Error(`Erro de rede ou servidor: ${response.status}`);
                }

            } catch (error) {
                // Este bloco captura erros de rede (ex: sem conexão) ou o erro lançado acima.
                console.error('Erro na requisição de edição:', error);
                alert('Ocorreu um erro de comunicação ao tentar salvar. Verifique sua conexão e tente novamente.');
            }
        });
    } else {
        console.warn("Elemento 'formEdicaoMeta' não encontrado. A funcionalidade de edição não funcionará.");
    }
});