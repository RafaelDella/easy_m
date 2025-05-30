<?php
// includes/header.php
// Este arquivo contém a estrutura HTML do cabeçalho.
// Não deve conter lógica de sessão ou banco de dados aqui.
// Os caminhos para os assets (imagens) são relativos à página que incluir este header.
?>
<div id="modalVisualizarEntrada" class="modal">
    <div class="modal-content">
        <span class="close" onclick="fecharModal('modalVisualizarEntrada')">&times;</span>
        <h2>Detalhes da Entrada</h2>
        <p><strong>Descrição:</strong> <span id="verDescricao"></span></p>
        <p><strong>Valor:</strong> R$ <span id="verValor"></span></p>
        <p><strong>Categoria:</strong> <span id="verCategoria"></span></p>
        <p><strong>Data:</strong> <span id="verDataEntrada"></span></p>
        <div class="modal-buttons" style="justify-content: flex-end;">
            <button type="button" class="btn-cancelar" onclick="fecharModal('modalVisualizarEntrada')">Fechar</button>
        </div>
    </div>
</div>

<script>
    function visualizarEntrada(id) {
        fetch(`../forms_entrada/5-carregar_entrada.php?id=${id}`) // Ajuste o caminho se necessário
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
                    document.getElementById('verValor').innerText = parseFloat(entrada.valor).toFixed(2).replace('.', ',');
                    document.getElementById('verCategoria').innerText = entrada.categoria;
                    document.getElementById('verDataEntrada').innerText = new Date(entrada.data_entrada).toLocaleDateString('pt-BR');

                    abrirModal('modalVisualizarEntrada'); // Usa a função global para abrir o modal
                } else {
                    alert('⚠ Erro: ' + data.erro);
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                alert('❌ Erro ao buscar entrada.');
            });
    }
</script>