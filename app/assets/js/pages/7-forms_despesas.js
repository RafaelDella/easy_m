// Funções para Modais
const cadastroModal = document.getElementById('cadastroModal');
const edicaoModal = document.getElementById('edicaoModal');
const visualizacaoModal = document.getElementById('visualizacaoModal');
const confirmModal = document.getElementById('confirmModal');

function abrirModal() {
    cadastroModal.style.display = 'block';
}

function fecharModal() {
    cadastroModal.style.display = 'none';
    document.getElementById('formCadastroDespesa').reset(); // Limpa o formulário
}

function abrirModalEdicao() {
    edicaoModal.style.display = 'block';
}

function fecharModalEdicao() {
    edicaoModal.style.display = 'none';
    document.getElementById('formEdicaoDespesa').reset(); // Limpa o formulário
}

function abrirModalVisualizacao() {
    visualizacaoModal.style.display = 'block';
}

function fecharModalVisualizacao() {
    visualizacaoModal.style.display = 'none';
}

function abrirModalExcluir() {
    confirmModal.style.display = 'block';
}

function fecharModalExcluir() {
    confirmModal.style.display = 'none';
}

// Fechar modais ao clicar fora
window.onclick = function(event) {
    if (event.target == cadastroModal) {
        fecharModal();
    } else if (event.target == edicaoModal) {
        fecharModalEdicao();
    } else if (event.target == visualizacaoModal) {
        fecharModalVisualizacao();
    } else if (event.target == confirmModal) {
        fecharModalExcluir();
    }
};

// Funções CRUD

// 1. Cadastrar Despesa
document.getElementById('formCadastroDespesa').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    try {
        const response = await fetch('2-cadastrar_despesa.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        alert(result.mensagem);
        if (result.sucesso) {
            fecharModal();
            location.reload(); // Recarrega a página para mostrar a nova despesa
        }
    } catch (error) {
        console.error('Erro ao cadastrar despesa:', error);
        alert('Erro ao cadastrar despesa.');
    }
});

// 2. Visualizar Despesa
async function visualizarDespesa(id) {
    try {
        const response = await fetch(`4-buscar_despesa.php?id=${id}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();

        if (result.sucesso) {
            const despesa = result.despesa;
            document.getElementById('view_id_despesa').textContent = despesa.id_despesa;
            document.getElementById('view_nome_despesa').textContent = despesa.nome_despesa;
            document.getElementById('view_descricao_despesa').textContent = despesa.descricao || 'N/A'; // N/A se vazio
            document.getElementById('view_valor_despesa').textContent = parseFloat(despesa.valor_despesa).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('view_data_vencimento_despesa').textContent = new Date(despesa.data_vencimento + 'T00:00:00').toLocaleDateString('pt-BR');
            document.getElementById('view_nome_categoria').textContent = despesa.nome_categoria; // Vem do JOIN no PHP

            abrirModalVisualizacao();
        } else {
            alert(result.mensagem);
        }
    } catch (error) {
        console.error('Erro na requisição AJAX para visualizarDespesa:', error);
        alert('Erro ao carregar detalhes da despesa.');
    }
}

// 3. Editar Despesa (preencher modal de edição)
async function editarDespesa(despesaData) {
    document.getElementById('edit_id_despesa').value = despesaData.id;
    document.getElementById('edit_nome_despesa').value = despesaData.nome;
    document.getElementById('edit_descricao_despesa').value = despesaData.descricao;
    document.getElementById('edit_valor_despesa').value = parseFloat(despesaData.valor).toFixed(2);
    document.getElementById('edit_data_vencimento_despesa').value = despesaData.data_vencimento;
    document.getElementById('edit_categoria_despesa').value = despesaData.id_categoria;

    abrirModalEdicao();
}

// 4. Salvar Edição da Despesa
document.getElementById('formEdicaoDespesa').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    try {
        const response = await fetch('3-editar_despesa.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        alert(result.mensagem);
        if (result.sucesso) {
            fecharModalEdicao();
            location.reload(); // Recarrega a página para mostrar as alterações
        }
    } catch (error) {
        console.error('Erro ao editar despesa:', error);
        alert('Erro ao editar despesa.');
    }
});

// A exclusão de despesa individual é feita por um <form> HTML direto para 5-excluir_despesa.php
// A exclusão de todas as despesas é feita por um <form> HTML direto para 6-deletar_todas_despesas.php