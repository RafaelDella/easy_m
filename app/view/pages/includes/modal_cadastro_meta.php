<div id="modalCadastroMeta" class="modal">
    <div class="modal-content">
        <span class="close-button" onclick="fecharModal('modalCadastroMeta')">&times;</span>
        <h2>Cadastrar Nova Meta</h2>
        <form id="formCadastroMeta">
            <label for="titulo_meta">Título:</label>
            <input type="text" id="titulo_meta" name="titulo" required>

            <label for="descricao_meta">Descrição:</label>
            <textarea id="descricao_meta" name="descricao"></textarea>

            <label for="categoria_meta">Categoria:</label>
            <input type="text" id="categoria_meta" name="categoria" required>

            <label for="valor_meta">Valor da Meta:</label>
            <input type="number" step="0.01" id="valor_meta" name="valor_meta" required>

            <label for="previsao_meta">Previsão de Conclusão:</label>
            <input type="date" id="previsao_meta" name="previsao_conclusao" required>

            <div class="modal-buttons">
                <button type="button" class="btn red" onclick="fecharModal('modalCadastroMeta')">Cancelar</button>
                <button type="submit" class="btn green">Cadastrar</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.getElementById("formCadastroMeta").addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("../../../view/pages/forms_meta/2-cadastrar_meta.php", {

                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Resposta do servidor:", data);
                alert("Meta cadastrada com sucesso!");
                fecharModal("modalCadastroMeta");
                this.reset();
                location.reload();
            })
            .catch(error => {
                console.error("Erro no cadastro:", error);
                alert("Erro ao cadastrar a meta.");
            });
    });
</script>