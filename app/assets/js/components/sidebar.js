// public/assets/js/sidebar.js

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const openBtn = document.getElementById('open_btn');
    const openBtnIcon = document.getElementById('open_btn_icon');


    if (openBtn) { // Garante que o botão existe antes de adicionar o listener
        openBtn.addEventListener('click', function() {
            if (sidebar) sidebar.classList.toggle('open-sidebar');
            if (openBtnIcon) {
                openBtnIcon.classList.toggle('fa-chevron-right');
                openBtnIcon.classList.toggle('fa-chevron-left');
            } // Chama a função para ajustar o layout
        });
    } else {
        console.warn("Botão 'open_btn' não encontrado. O controle da sidebar pode não funcionar.");
    }

});