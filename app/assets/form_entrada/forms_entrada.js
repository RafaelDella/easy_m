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
