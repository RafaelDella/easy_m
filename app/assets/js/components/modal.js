class CustomModal {
    constructor() {
        // Criação da estrutura base do modal
        this.modalOverlay = document.createElement('div');
        this.modalOverlay.className = 'modal-overlay'; // Classe para o fundo escuro

        this.modalContainer = document.createElement('div');
        this.modalContainer.className = 'modal-container'; // Classe para o container do modal

        // Header do modal: contém ícone, título principal e subtítulo/mensagem
        this.modalHeader = document.createElement('div');
        this.modalHeader.className = 'modal-header';

        this.modalIconWrapper = document.createElement('div'); // Wrapper para o ícone (para estilização de fundo)
        this.modalIconWrapper.className = 'modal-icon-wrapper';

        this.modalIcon = document.createElement('span'); // Onde o ícone Font Awesome será inserido
        this.modalIcon.className = 'modal-icon';
        this.modalIconWrapper.appendChild(this.modalIcon);

        this.modalHeaderContent = document.createElement('div'); // Wrapper para o texto do header
        this.modalHeaderContent.className = 'modal-header-content';

        this.modalTitle = document.createElement('h2'); // Título principal do modal
        this.modalTitle.className = 'modal-title';

        this.modalHeaderMessage = document.createElement('p'); // Mensagem/subtítulo no header
        this.modalHeaderMessage.className = 'modal-header-message';

        this.modalHeaderContent.appendChild(this.modalTitle);
        this.modalHeaderContent.appendChild(this.modalHeaderMessage);

        this.modalHeader.appendChild(this.modalIconWrapper);
        this.modalHeader.appendChild(this.modalHeaderContent);

        // Conteúdo principal do modal (pode ser usado para texto adicional ou justificação)
        this.modalContent = document.createElement('div');
        this.modalContent.className = 'modal-content';

        // Rodapé do modal: contém o botão de fechar
        this.modalFooter = document.createElement('div');
        this.modalFooter.className = 'modal-footer';

        this.modalButton = document.createElement('button'); // Botão de fechar
        this.modalButton.className = 'modal-button modal-button-primary';
        this.modalButton.textContent = 'Fechar';

        // Adiciona um event listener para o botão de fechar
        this.modalButton.addEventListener('click', () => this.hide());
        this.modalFooter.appendChild(this.modalButton);

        // Montagem final da estrutura do modal
        this.modalContainer.appendChild(this.modalHeader);
        this.modalContainer.appendChild(this.modalContent);
        this.modalContainer.appendChild(this.modalFooter);
        this.modalOverlay.appendChild(this.modalContainer);
        document.body.appendChild(this.modalOverlay); // Adiciona o modal ao corpo do documento

        // Oculta o modal inicialmente
        this.hide();

        // Permite fechar o modal clicando fora da área do container
        this.modalOverlay.addEventListener('click', (e) => {
            if (e.target === this.modalOverlay) {
                this.hide();
            }
        });

        this.onCloseCallback = null; // Propriedade para armazenar a função de callback
    }

    /**
     * Exibe o modal com o tipo, título, mensagem e um callback opcional ao fechar.
     * @param {string} type - Tipo do modal ('success', 'error', 'warning', 'info').
     * @param {string} title - Título principal do modal.
     * @param {string} message - Mensagem/subtítulo do modal.
     * @param {function} [onCloseCallback=null] - Função a ser executada quando o modal for fechado.
     */
    show(type, title, message, onCloseCallback = null) {
        // Mapeia tipos para ícones Font Awesome
        const iconMap = {
            success: '<i class="fa-solid fa-circle-check"></i>',
            error: '<i class="fa-solid fa-xmark"></i>',
            warning: '<i class="fa-solid fa-triangle-exclamation"></i>',
            info: '<i class="fa-solid fa-circle-info"></i>'
        };

        // Garante que o tipo seja um dos válidos, caso contrário, usa 'info'
        const iconClass = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';

        // Aplica a classe de estilo e o ícone HTML
        this.modalIcon.className = `modal-icon ${iconClass}`;
        this.modalIcon.innerHTML = iconMap[iconClass] || iconMap.info;

        // Define o título e a mensagem do header
        this.modalTitle.textContent = title;
        this.modalHeaderMessage.textContent = message;

        // Limpa o conteúdo principal, pois a mensagem principal agora está no header
        this.modalContent.textContent = '';

        // Adiciona a classe do tipo ao wrapper do ícone para estilização de cores de fundo
        this.modalIconWrapper.className = `modal-icon-wrapper ${iconClass}`;

        this.onCloseCallback = onCloseCallback; // Armazena o callback

        // Exibe o overlay do modal
        this.modalOverlay.classList.add('active');
    }

    // Oculta o modal
    hide() {
        this.modalOverlay.classList.remove('active');
        // Executa o callback se ele existir e for uma função
        if (this.onCloseCallback && typeof this.onCloseCallback === 'function') {
            this.onCloseCallback();
            this.onCloseCallback = null; // Limpa o callback após a execução para evitar chamadas duplicadas
        }
    }
}

// Inicializador global para criar uma instância do modal e expor a função customAlert
function initModal() {
    // Evita múltiplas inicializações
    if (window.customAlert) return;

    const modalInstance = new CustomModal();

    // Expõe uma função global para facilitar o uso do modal, agora aceitando um callback
    window.customAlert = (message, title = 'Alerta', type = 'info', onCloseCallback = null) => {
        if (modalInstance) {
            modalInstance.show(type, title, message, onCloseCallback);
        } else {
            // Fallback para alert() caso o modal não esteja disponível (apenas para debug)
            console.warn('Modal não disponível, usando alert');
            alert(`${title}: ${message}`);
            // Executa o callback mesmo no fallback, se fornecido
            if (onCloseCallback && typeof onCloseCallback === 'function') {
                onCloseCallback();
            }
        }
    };
}

// Inicializa o modal quando o DOM estiver completamente carregado
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModal);
} else {
    initModal(); // Se o DOM já estiver carregado (ex: script no final do body)
}

