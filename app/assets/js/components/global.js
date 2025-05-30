document.addEventListener("DOMContentLoaded", function() {

    // Função genérica para abrir modal
    window.abrirModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "flex";
        }
    };

    // Função genérica para fechar modal
    window.fecharModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = "none";
        }
    };

    // Fechar modal ao clicar fora do conteúdo do modal (para todos os modais)
    window.addEventListener('click', function(event) {
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Adiciona o ano atual e anteriores ao select de ano
    const selectAno = document.getElementById("ano");
    if (selectAno) {
        const anoAtual = new Date().getFullYear();
        // Adiciona o ano atual como a primeira opção
        let optionAtual = document.createElement("option");
        optionAtual.value = anoAtual;
        optionAtual.textContent = anoAtual;
        selectAno.appendChild(optionAtual);

        for (let ano = anoAtual - 1; ano >= 2000; ano--) { // Loop a partir do ano anterior
            const option = document.createElement("option");
            option.value = ano;
            option.textContent = ano;
            selectAno.appendChild(option);
        }
    }
});