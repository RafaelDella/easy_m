// form_profile_logic.js
// Lógica para navegação e validação do formulário de perfil financeiro.

// Seleciona os elementos do formulário
const steps = document.querySelectorAll(".form-step");
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
const submitBtn = document.getElementById("submitBtn");

let currentStep = 0;

// Função para mostrar a etapa atual do formulário e controlar a visibilidade dos botões
function showStep(step) {
    steps.forEach((el, i) => {
        el.classList.toggle("active", i === step); // Adiciona/remove a classe 'active'
    });
    // Controla a visibilidade do botão "Voltar"
    prevBtn.style.display = step > 0 ? "inline-block" : "none";
    // Controla a visibilidade do botão "Próximo"
    nextBtn.style.display = step < steps.length - 1 ? "inline-block" : "none";
    // Controla a visibilidade do botão "Enviar" (apenas na última etapa)
    submitBtn.style.display = step === steps.length - 1 ? "inline-block" : "none";
}

// Event listener para o botão "Próximo"
nextBtn.addEventListener("click", () => {
    const radios = steps[currentStep].querySelectorAll("input[type='radio']");
    const nameSet = new Set(); // Usado para rastrear grupos de rádio únicos
    let allAnswered = true; // Flag para verificar se todas as perguntas foram respondidas

    radios.forEach((radio) => {
        // Verifica se já processou este grupo de rádio
        if (!nameSet.has(radio.name)) {
            nameSet.add(radio.name); // Adiciona o nome do grupo ao Set
            // Verifica se algum rádio neste grupo foi selecionado
            const groupChecked = steps[currentStep].querySelector(`input[name="${radio.name}"]:checked`);
            if (!groupChecked) {
                allAnswered = false; // Se não houver resposta, marca como não respondido
            }
        }
    });

    // Se nem todas as perguntas foram respondidas, exibe a modal e impede o avanço
    if (!allAnswered) {
        // Usa a função global customAlert do seu CustomModal
        window.customAlert("Por favor, selecione uma opção para cada pergunta.", "Atenção!", "warning");
        return; // Sai da função, impedindo o avanço
    }

    currentStep++; // Avança para a próxima etapa
    showStep(currentStep); // Exibe a nova etapa
});

// Event listener para o botão "Voltar"
prevBtn.addEventListener("click", () => {
    currentStep--; // Volta para a etapa anterior
    showStep(currentStep); // Exibe a nova etapa
});

// Exibe a primeira etapa do formulário quando a página carrega
showStep(currentStep);
