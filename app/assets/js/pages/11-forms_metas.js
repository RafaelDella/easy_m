// Exemplo de como a função visualizarMeta deveria ser (assumindo que ela faz uma requisição AJAX)

function visualizarMeta(idMeta) {
    // Abre o modal de visualização imediatamente
    abrirModal('modalVisualizacaoMeta'); 

    // Limpa os campos de visualização enquanto carrega
    document.getElementById('view_titulo_meta').textContent = 'Carregando...';
    document.getElementById('view_descricao_meta').textContent = 'Carregando...';
    document.getElementById('view_categoria_meta').textContent = 'Carregando...';
    document.getElementById('view_valor_meta').textContent = 'Carregando...';
    document.getElementById('view_previsao_meta').textContent = 'Carregando...';

    // Faz a requisição AJAX para buscar os detalhes da meta
    fetch(`../../../view/pages/forms_meta/4-carregar_meta.php?id=${idMeta}`) // Supondo que você tenha um endpoint para buscar a meta
        .then(response => {
            if (!response.ok) {
                // Tenta ler a resposta como texto para ver a mensagem de erro do servidor
                return response.text().then(text => {
                    throw new Error('Erro ao carregar meta: ' + response.status + ' - ' + text);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.sucesso && data.meta) {
                // Preenche o modal com os dados da meta
                document.getElementById('view_titulo_meta').textContent = data.meta.titulo;
                document.getElementById('view_descricao_meta').textContent = data.meta.descricao;
                document.getElementById('view_categoria_meta').textContent = data.meta.categoria;
                document.getElementById('view_valor_meta').textContent = parseFloat(data.meta.valor_meta).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('view_previsao_meta').textContent = new Date(data.meta.previsao_conclusao).toLocaleDateString('pt-BR');
            } else {
                console.error('Erro no servidor ou meta não encontrada:', data.erro || 'Meta não encontrada.');
                alert('Erro ao carregar detalhes da meta.');
                fecharModal('modalVisualizacaoMeta');
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('Não foi possível carregar os detalhes da meta. Verifique o console para mais informações.');
            fecharModal('modalVisualizacaoMeta');
            
        });
}

// Suas outras funções como `editarMeta`, `abrirModal`, `fecharModal`, `abrirModalConfirmacao`
// também devem estar neste ou em um arquivo global carregado antes.
// Por exemplo, as funções `abrirModal`, `fecharModal`, `abrirModalConfirmacao` que você já usa no `global.js`
// precisam estar acessíveis para `visualizarMeta`.

// --- Função para ABRIR o modal de edição e preencher os campos ---
function editarMeta(meta) {
    // Preenche os campos do formulário de edição com os dados da meta
    document.getElementById('editar_id_meta').value = meta.id;
    document.getElementById('editar_titulo_meta').value = meta.titulo;
    document.getElementById('editar_descricao_meta').value = meta.descricao;
    document.getElementById('editar_categoria_meta').value = meta.categoria;
    document.getElementById('editar_valor_meta').value = meta.valor_meta; // O input number lida com o formato
    document.getElementById('editar_previsao_meta').value = meta.previsao_conclusao; // O input type="date" espera YYYY-MM-DD

    // Abre o modal de edição
    abrirModal('modalEdicaoMeta'); // Certifique-se de que 'abrirModal' esteja definida em global.js
}

// --- Listener para SUBMISSÃO do formulário de edição ---
// É uma boa prática adicionar listeners após o DOM estar carregado.
// Se este script (11-forms_metas.js) é carregado na <head>, use DOMContentLoaded.
// Se ele é carregado no final do <body>, pode adicionar diretamente.
document.addEventListener('DOMContentLoaded', () => {
    const formEdicaoMeta = document.getElementById('formEdicaoMeta');

    if (formEdicaoMeta) {
        formEdicaoMeta.addEventListener('submit', async (event) => {
            event.preventDefault(); // Impede o envio padrão do formulário

            const idMeta = document.getElementById('editar_id_meta').value;
            const titulo = document.getElementById('editar_titulo_meta').value;
            const descricao = document.getElementById('editar_descricao_meta').value;
            const categoria = document.getElementById('editar_categoria_meta').value;
            const valorMeta = document.getElementById('editar_valor_meta').value;
            const previsaoConclusao = document.getElementById('editar_previsao_meta').value;

            // Cria um objeto FormData para enviar os dados via POST
            const formData = new FormData();
            formData.append('id_meta', idMeta);
            formData.append('titulo', titulo);
            formData.append('descricao', descricao);
            formData.append('categoria', categoria);
            formData.append('valor_meta', valorMeta);
            formData.append('previsao_conclusao', previsaoConclusao);

            try {
                const response = await fetch('../../../view/pages/forms_meta/5-atualizar_meta.php   ', { // AJUSTE ESTE CAMINHO se 'atualizar_meta.php' não estiver na mesma pasta!
                    method: 'POST',
                    body: formData // Envia os dados do formulário
                });

                if (!response.ok) {
                    // Se houver um erro HTTP (4xx ou 5xx), tenta ler a mensagem de erro do servidor
                    const errorText = await response.text();
                    throw new Error(`Erro do servidor (${response.status}): ${errorText}`);
                }

                const data = await response.json(); // Tenta parsear a resposta como JSON

                if (data.sucesso) {
                    alert('Meta atualizada com sucesso!');
                    fecharModal('modalEdicaoMeta'); // Fecha o modal após o sucesso
                    location.reload(); // Recarrega a página para mostrar os dados atualizados
                } else {
                    alert('Erro ao atualizar meta: ' + (data.erro || 'Erro desconhecido.'));
                    console.error('Erro na resposta da API:', data.erro);
                }
            } catch (error) {
                console.error('Erro na requisição de edição:', error);
                alert('Não foi possível atualizar a meta. Verifique o console para mais detalhes.');
            }
        });
    } else {
        console.warn("Elemento 'formEdicaoMeta' não encontrado. Verifique o HTML do modal de edição.");
    }
});