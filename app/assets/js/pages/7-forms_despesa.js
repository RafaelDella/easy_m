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
        const response = await fetch('../../../view/pages/forms_despesa/2-processar_cadastro_despesa.php', {
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
        const response = await fetch(`../../../view/pages/forms_despesa/4-listar_categorias_despesa.php?id=${id}`);
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
        const response = await fetch('../../../view/pages/forms_despesa/4-editar_despesa.php', {
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

const categoriasModal = document.getElementById('categoriasModal');
const listaCategoriasUl = document.getElementById('lista_categorias');
const novaCategoriaInput = document.getElementById('nova_categoria_nome');
const btnAdicionarCategoria = document.getElementById('btn_adicionar_categoria');

function abrirModalCategorias() {
    categoriasModal.style.display = 'block';
    carregarCategoriasParaGerenciamento(); // Carrega a lista quando o modal abre
}

function fecharModalCategorias() {
    categoriasModal.style.display = 'none';
    novaCategoriaInput.value = ''; // Limpa o campo
    atualizarSelectsCategorias(); // Atualiza os selects de despesas
}

// Fechar modais ao clicar fora (adicionado o novo modal)
window.onclick = function(event) {
    if (event.target == cadastroModal) {
        fecharModal();
    } else if (event.target == edicaoModal) {
        fecharModalEdicao();
    } else if (event.target == visualizacaoModal) {
        fecharModalVisualizacao();
    } else if (event.target == confirmModal) {
        fecharModalExcluir();
    } else if (event.target == categoriasModal) { // NOVO
        fecharModalCategorias(); // NOVO
    }
};


// FUNÇÕES CRUD DE CATEGORIAS



// Carregar categorias para a lista no modal de gerenciamento
async function carregarCategoriasParaGerenciamento() {
    try {
        const response = await fetch('../../../view/pages/forms_categoria_despesa/4-listar_categorias_despesa.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();

        if (result.sucesso) {
            listaCategoriasUl.innerHTML = ''; // Limpa a lista existente
            result.categorias.forEach(categoria => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span id="cat_nome_${categoria.id_categoria}">${categoria.nome_categoria}</span>
                    <div class="categoria-actions">
                        <button class="btn blue btn-small" onclick="editarCategoria(${categoria.id_categoria}, '${categoria.nome_categoria}')"><i class="fa-solid fa-pen"></i></button>
                        <button class="btn red btn-small" onclick="excluirCategoria(${categoria.id_categoria}, '${categoria.nome_categoria}')"><i class="fa-solid fa-trash"></i></button>
                    </div>
                `;
                listaCategoriasUl.appendChild(li);
            });
        } else {
            alert('Erro ao carregar categorias: ' + result.mensagem);
        }
    } catch (error) {
        console.error('Erro ao carregar categorias:', error);
        alert('Erro ao carregar categorias.');
    }
}

// Adicionar Categoria
btnAdicionarCategoria.addEventListener('click', async function() {
    const nome = novaCategoriaInput.value.trim();
    if (nome === '') {
        alert('O nome da categoria não pode ser vazio.');
        return;
    }

    const formData = new FormData();
    formData.append('nome_categoria', nome);

    try {
        const response = await fetch('../../../view/pages/forms_categoria_despesa/1-cadastrar_categoria_despesa.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        alert(result.mensagem);
        if (result.sucesso) {
            novaCategoriaInput.value = ''; // Limpa o campo
            carregarCategoriasParaGerenciamento(); // Recarrega a lista no modal
            // Não recarrega a página toda, só atualiza os selects depois de fechar o modal
        }
    } catch (error) {
        console.error('Erro ao adicionar categoria:', error);
        alert('Erro ao adicionar categoria.');
    }
});

// Editar Categoria
async function editarCategoria(id, nomeAtual) {
    const novoNome = prompt('Digite o novo nome para a categoria:', nomeAtual);
    if (novoNome === null || novoNome.trim() === '') {
        if (novoNome === null) return; // Usuário cancelou
        alert('O nome da categoria não pode ser vazio.');
        return;
    }

    const formData = new FormData();
    formData.append('id_categoria', id);
    formData.append('nome_categoria', novoNome.trim());

    try {
        const response = await fetch('../../../view/pages/forms_categoria_despesa/2-editar_categoria_despesa.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        alert(result.mensagem);
        if (result.sucesso) {
            // Atualiza o nome diretamente na lista sem recarregar tudo
            document.getElementById(`cat_nome_${id}`).textContent = result.nome_categoria;
            // Não recarrega a página toda, só atualiza os selects depois de fechar o modal
        }
    } catch (error) {
        console.error('Erro ao editar categoria:', error);
        alert('Erro ao editar categoria.');
    }
}

// Excluir Categoria
async function excluirCategoria(id, nome) {
    if (!confirm(`Tem certeza que deseja excluir a categoria "${nome}"?\nTodas as despesas associadas a esta categoria também serão afetadas (devido à chave estrangeira ON DELETE CASCADE).`)) {
        return;
    }

    const formData = new FormData();
    formData.append('id_categoria', id);

    try {
        const response = await fetch('../../../view/pages/forms_categoria_despesa/3-excluir_categoria_despesa.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        alert(result.mensagem);
        if (result.sucesso) {
            carregarCategoriasParaGerenciamento(); // Recarrega a lista
            // Não recarrega a página toda, só atualiza os selects depois de fechar o modal
        }
    } catch (error) {
        console.error('Erro ao excluir categoria:', error);
        alert('Erro ao excluir categoria.');
    }
}


// Função para atualizar os selects de categoria em tempo real
async function atualizarSelectsCategorias() {
    try {
        const response = await fetch('../../../view/pages/forms_categoria_despesa/4-listar_categoria_despesa.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();

        if (result.sucesso) {
            const selects = [
                document.querySelector('select[name="categoria"]'), // Filtro
                document.getElementById('categoria_despesa'),     // Cadastro
                document.getElementById('edit_categoria_despesa') // Edição
            ];

            selects.forEach(select => {
                if (select) {
                    const selectedValue = select.value; // Guarda o valor selecionado
                    select.innerHTML = '<option value="">Selecione a Categoria</option>'; // Limpa

                    result.categorias.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.id_categoria;
                        option.textContent = cat.nome_categoria;
                        select.appendChild(option);
                    });
                    select.value = selectedValue; // Restaura o valor selecionado
                }
            });
        } else {
            console.error('Erro ao atualizar selects de categoria:', result.mensagem);
        }
    } catch (error) {
        console.error('Erro na requisição AJAX para atualizar selects de categoria:', error);
    }
}
