class CustomModal {
    constructor() {
        // Criação da estrutura do modal
        this.modalOverlay = document.createElement('div');
        this.modalOverlay.className = 'modal-overlay';

        this.modalContainer = document.createElement('div');
        this.modalContainer.className = 'modal-container';

        // Header (ícone + título principal + subtítulo/descrição do erro)
        this.modalHeader = document.createElement('div');
        this.modalHeader.className = 'modal-header';

        this.modalIconWrapper = document.createElement('div'); // Novo wrapper para o ícone
        this.modalIconWrapper.className = 'modal-icon-wrapper';

        this.modalIcon = document.createElement('span');
        this.modalIcon.className = 'modal-icon';
        this.modalIconWrapper.appendChild(this.modalIcon); // Ícone dentro do novo wrapper

        this.modalHeaderContent = document.createElement('div'); // Novo wrapper para título e mensagem do header
        this.modalHeaderContent.className = 'modal-header-content';

        this.modalTitle = document.createElement('h2');
        this.modalTitle.className = 'modal-title';

        this.modalHeaderMessage = document.createElement('p'); // Nova tag para a mensagem do header
        this.modalHeaderMessage.className = 'modal-header-message';


        this.modalHeaderContent.appendChild(this.modalTitle);
        this.modalHeaderContent.appendChild(this.modalHeaderMessage); // Adiciona a nova tag


        this.modalHeader.appendChild(this.modalIconWrapper); // Adiciona o wrapper do ícone
        this.modalHeader.appendChild(this.modalHeaderContent); // Adiciona o wrapper do conteúdo do header

        // Conteúdo principal (para a justificativa do erro)
        this.modalContent = document.createElement('div');
        this.modalContent.className = 'modal-content';

        // Rodapé (botão)
        this.modalFooter = document.createElement('div');
        this.modalFooter.className = 'modal-footer';

        this.modalButton = document.createElement('button');
        this.modalButton.className = 'modal-button modal-button-primary';
        this.modalButton.textContent = 'Fechar';

        this.modalButton.addEventListener('click', () => this.hide());
        this.modalFooter.appendChild(this.modalButton);

        // Montagem final
        this.modalContainer.appendChild(this.modalHeader);
        this.modalContainer.appendChild(this.modalContent);
        this.modalContainer.appendChild(this.modalFooter);
        this.modalOverlay.appendChild(this.modalContainer);
        document.body.appendChild(this.modalOverlay);

        // Oculta inicialmente
        this.hide();

        // Permite fechar clicando fora
        this.modalOverlay.addEventListener('click', (e) => {
            if (e.target === this.modalOverlay) {
                this.hide();
            }
        });
    }

    show(type, title, message) {
        // Define ícones com HTML
        const iconMap = {
            success: '<i class="fa-solid fa-circle-check"></i>',
            error: '<i class="fa-solid fa-xmark"></i>',
            warning: '<i class="fa-solid fa-triangle-exclamation"></i>',
            info: '<i class="fa-solid fa-circle-info"></i>'
        };

        const iconClass = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';

        // Aplica classe e ícone
        this.modalIcon.className = `modal-icon ${iconClass}`;
        this.modalIcon.innerHTML = iconMap[iconClass] || iconMap.info;

        // O título principal agora é o 'title'
        this.modalTitle.textContent = title;
        // A mensagem do header agora é a 'message'
        this.modalHeaderMessage.textContent = message;

        // O conteúdo principal (que você usou para a mensagem) pode ser deixado vazio
        // ou usado para uma descrição mais longa se necessário.
        // Se você quiser que a justificativa do erro apareça no modalContent,
        // você precisará de outro parâmetro na função show ou mudar a lógica.
        // Por enquanto, o modalContent ficará sem texto se 'message' for movido para o header.
        this.modalContent.textContent = ''; // Limpa o conteúdo principal se não for usado

        // Adiciona a classe do tipo ao modalIconWrapper para estilização de cores
        this.modalIconWrapper.className = `modal-icon-wrapper ${iconClass}`;


        this.modalOverlay.classList.add('active');
    }

    hide() {
        this.modalOverlay.classList.remove('active');
    }
}

// Inicializador global
function initModal() {
    if (window.customAlert) return;

    const modalInstance = new CustomModal();

    window.customAlert = (message, title = 'Alerta', type = 'info') => {
        if (modalInstance) {
            modalInstance.show(type, title, message); // 'message' agora vai para o header
        } else {
            console.warn('Modal não disponível, usando alert');
            alert(`${title}: ${message}`);
        }
    };
}

// Inicializa ao carregar DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModal);
} else {
    initModal();
}