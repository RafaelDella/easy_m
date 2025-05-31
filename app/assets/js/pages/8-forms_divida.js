document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formDivida");

    if (form) {
        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const dados = {
                nome_divida: form.nome_divida.value,
                valor_total: form.valor_total.value,
                valor_pago: form.valor_pago.value,
                taxa_divida: form.taxa_divida.value,
                data_vencimento: form.data_vencimento.value,
                categoria_divida: form.categoria_divida.value
            };

            const resposta = await fetch("../2-cadastrar_divida.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(dados)
            });

            const resultado = await resposta.json();
            alert(resultado.mensagem);
            if (resultado.sucesso) {
                form.reset();
                fecharModal('modalCadastroDivida');
                location.reload(); // Atualiza a lista após cadastro
            }
        });
    }

    // Modal de edição
    window.editarDivida = function (divida) {
        document.getElementById("edit_id_divida").value = divida.id_divida;
        document.getElementById("edit_nome_divida").value = divida.nome_divida;
        document.getElementById("edit_valor_total").value = divida.valor_total;
        document.getElementById("edit_valor_pago").value = divida.valor_pago;
        document.getElementById("edit_taxa_divida").value = divida.taxa_divida;
        document.getElementById("edit_categoria_divida").value = divida.categoria_divida;
        document.getElementById("edit_data_vencimento").value = divida.data_vencimento;

        abrirModal('modalEditarDivida');
    };

    // Modal de visualização
    window.visualizarDivida = function (id) {
        fetch(`5-carregar_divida.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    const d = data.divida;
                    document.getElementById("view_nome_divida").innerText = d.nome_divida;
                    document.getElementById("view_valor_total").innerText = parseFloat(d.valor_total).toLocaleString("pt-BR", { minimumFractionDigits: 2 });
                    document.getElementById("view_valor_pago").innerText = parseFloat(d.valor_pago).toLocaleString("pt-BR", { minimumFractionDigits: 2 });
                    document.getElementById("view_taxa_divida").innerText = parseFloat(d.taxa_divida).toFixed(2) + "%";
                    document.getElementById("view_categoria_divida").innerText = d.categoria_divida;
                    document.getElementById("view_data_vencimento").innerText = new Date(d.data_vencimento).toLocaleDateString("pt-BR");

                    abrirModal("modalVisualizarDivida");
                } else {
                    alert("Erro: " + data.mensagem);
                }
            })
            .catch(err => {
                console.error("Erro ao carregar dívida:", err);
                alert("Erro ao carregar detalhes da dívida.");
            });
    };
});
