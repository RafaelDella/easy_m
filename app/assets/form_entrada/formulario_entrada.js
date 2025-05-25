function validarFormulario() {
    const descricao = document.getElementById('descricao').value.trim();
    const valor = parseFloat(document.getElementById('valor').value);
    const categoria = document.getElementById('categoria').value;
    const data = document.getElementById('data_entrada').value;

    if (!descricao || !categoria || !data || isNaN(valor) || valor <= 0) {
        alert("Por favor, preencha todos os campos corretamente!");
        return false;
    }
    return true;
}

document.addEventListener("DOMContentLoaded", async () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");
    const tipo = params.get("tipo");

    if (id && tipo === "receita") {
        try {
            const resposta = await fetch(`carregar_entrada.php?id=${id}`);
            const dados = await resposta.json();

            if (dados.sucesso) {
                document.getElementById("entrada_id").value = dados.entrada.id_entrada;
                document.getElementById("descricao").value = dados.entrada.descricao;
                document.getElementById("valor").value = dados.entrada.valor;
                document.getElementById("categoria").value = dados.entrada.categoria;
                document.getElementById("data_entrada").value = dados.entrada.data_entrada;
                document.getElementById("btn-submit").textContent = "Salvar Alterações";
                document.querySelector("h2").textContent = "Editar Entrada";
            } else {
                alert("Erro ao carregar entrada.");
            }
        } catch (err) {
            alert("Erro de comunicação com o servidor.");
        }
    }
});

document.getElementById('open_btn').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('open-sidebar');
});

function abrirModal() {
    document.getElementById("modalEntrada").style.display = "flex";
}

function fecharModal() {
    document.getElementById("modalEntrada").style.display = "none";
}

// Fecha o modal ao clicar fora da área dele
window.onclick = function(event) {
    const modal = document.getElementById("modalEntrada");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}

// Validação básica do formulário
function validarFormulario() {
    const descricao = document.getElementById("descricao").value.trim();
    const valor = document.getElementById("valor").value.trim();
    const categoria = document.getElementById("categoria").value;
    const data = document.getElementById("data_entrada").value;

    if (!descricao || !valor || !categoria || !data) {
        alert("Por favor, preencha todos os campos.");
        return false;
    }
    return true;
}

window.onclick = function(event) {
  const modal = document.getElementById("modalEntrada");
  if (event.target == modal) {
    fecharModal();
  }
}

function editarEntrada(entrada) {
    document.getElementById("editar_entrada_id").value = entrada.id;
    document.getElementById("editar_descricao").value = entrada.descricao;
    document.getElementById("editar_valor").value = entrada.valor;
    document.getElementById("editar_categoria").value = entrada.categoria;
    document.getElementById("editar_data_entrada").value = entrada.data_entrada;

    document.getElementById("modalEditarEntrada").style.display = "block";
}

function fecharModalEditar() {
    document.getElementById("modalEditarEntrada").style.display = "none";
}


function abrirModalEditar(entrada) {
    document.getElementById("editar_entrada_id").value = entrada.id;
    document.getElementById("editar_descricao").value = entrada.descricao;
    document.getElementById("editar_valor").value = entrada.valor;
    document.getElementById("editar_categoria").value = entrada.categoria;
    document.getElementById("editar_data_entrada").value = entrada.data_entrada;
    
    document.getElementById("modalEditarEntrada").style.display = "block";
}

function fecharModalEditar() {
    document.getElementById("modalEditarEntrada").style.display = "none";
}

function validarFormularioEditar() {
    // Adicione validações extras se quiser
    return true;
}

// Fecha o modal ao clicar fora da área do conteúdo
window.addEventListener('click', function(event) {
const modalEditar = document.getElementById('modalEditarEntrada');
if (event.target === modalEditar) {
    modalEditar.style.display = 'none';
}
});

function fecharModalEditar() {
document.getElementById('modalEditarEntrada').style.display = 'none';
}

function visualizarEntrada(id) {
    fetch(`http://localhost/easy_m/app/view/form_entrada/buscar_entrada.php?id=${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.sucesso) {
                const entrada = data.entrada;
                document.getElementById('verDescricao').innerText = entrada.descricao;
                document.getElementById('verValor').innerText = `R$ ${parseFloat(entrada.valor).toFixed(2)}`;
                document.getElementById('verCategoria').innerText = entrada.categoria;
                document.getElementById('verDataEntrada').innerText = entrada.data_entrada;

                document.getElementById('modalVisualizar').style.display = 'block';
            } else {
                alert('⚠ Erro: ' + data.erro);
            }
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
            alert('❌ Erro ao buscar entrada.');
        });
}

function fecharModalVisualizar() {
    document.getElementById('modalVisualizar').style.display = 'none';
}

window.addEventListener('click', function(event) {
    const modal = document.getElementById('modalVisualizar');
    const content = document.querySelector('#modalVisualizar .modal-content');

    if (event.target === modal) {
        fecharModalVisualizar();
    }
});

document.getElementById('campo-busca').addEventListener('input', function () {
    const termo = this.value.toLowerCase();
    const cards = document.querySelectorAll('.session-card');

    cards.forEach(card => {
        const texto = card.textContent.toLowerCase();
        card.style.display = texto.includes(termo) ? 'block' : 'none';
    });
});
