// public/assets/js/sidebar.js

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const openBtn = document.getElementById('open_btn');
    const openBtnIcon = document.getElementById('open_btn_icon');
    const mainContent = document.querySelector('main');
    const topHeader = document.getElementById('top-header');

    // Função para ajustar o padding-left do main e do header
    function adjustContentPadding() {
        if (!sidebar || !mainContent || !topHeader) { // Checa se os elementos existem
            console.warn("Elementos da sidebar não encontrados. O ajuste de padding não será aplicado.");
            return;
        }

        if (sidebar.classList.contains('open-sidebar')) {
            mainContent.style.marginLeft = '220px'; // Largura da sidebar aberta
            topHeader.style.paddingLeft = '270px'; // Largura da sidebar aberta + padding extra
        } else {
            mainContent.style.marginLeft = '82px'; // Largura da sidebar fechada
            topHeader.style.paddingLeft = '132px'; // Largura da sidebar fechada + padding extra
        }
    }

    if (openBtn) { // Garante que o botão existe antes de adicionar o listener
        openBtn.addEventListener('click', function() {
            if (sidebar) sidebar.classList.toggle('open-sidebar');
            if (openBtnIcon) {
                openBtnIcon.classList.toggle('fa-chevron-right');
                openBtnIcon.classList.toggle('fa-chevron-left');
            }
            adjustContentPadding(); // Chama a função para ajustar o layout
        });
    } else {
        console.warn("Botão 'open_btn' não encontrado. O controle da sidebar pode não funcionar.");
    }

    // Chama a função uma vez no carregamento inicial para definir o estado correto
    adjustContentPadding();
});