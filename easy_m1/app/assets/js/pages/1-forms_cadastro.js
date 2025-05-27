document.addEventListener("DOMContentLoaded", function () {
    // Garantir que customAlert esteja disponível
    if (typeof customAlert !== 'function') {
        console.warn('customAlert ainda não definido, fallback ativado');
        window.customAlert = function (msg, title, type) {
            alert(`${title}: ${msg}`);
        };
    }

    const form = document.getElementById("formCadastro");

    if (!form) {
        console.error('Formulário não encontrado');
        return;
    }

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const getElement = (id) => {
            const el = document.getElementById(id);
            if (!el) console.error(`Elemento ${id} não encontrado`);
            return el;
        };

        const nomeInput = getElement("nome");
        const emailInput = getElement("email");
        const usuarioInput = getElement("usuario");
        const cpfInput = getElement("cpf");
        const senhaInput = getElement("senha");
        const confirmarSenhaInput = getElement("confirmar_senha");
        const escolaridadeInput = getElement("escolaridade");
        const dataNascimentoInput = getElement("data_nascimento");

        if (!nomeInput || !emailInput || !usuarioInput || !cpfInput ||
            !senhaInput || !confirmarSenhaInput || !escolaridadeInput || !dataNascimentoInput) {
            customAlert("Erro no formulário. Recarregue a página.", "Erro", "error");
            return;
        }

        const botaoCadastrar = form.querySelector("button[type='submit']");
        if (!botaoCadastrar) {
            console.error('Botão de submit não encontrado');
            return;
        }

        let nome = nomeInput.value.trim();
        const email = emailInput.value.trim();
        const usuario = usuarioInput.value.trim();
        const cpf = cpfInput.value.trim();
        const senha = senhaInput.value;
        const confirmarSenha = confirmarSenhaInput.value;
        const escolaridade = escolaridadeInput.value;
        const dataNascimento = dataNascimentoInput.value;

        // Capitaliza o nome
        nome = nome.toLowerCase()
            .split(' ')
            .map(palavra => palavra.charAt(0).toUpperCase() + palavra.slice(1))
            .join(' ');
        nomeInput.value = nome;

        // Validações
        if (!nome || !email || !usuario || !cpf || !senha || !confirmarSenha || !escolaridade || !dataNascimento) {
            customAlert("Todos os campos são obrigatórios.", "Atenção", "warning");
            return;
        }

        if (senha.length < 8) {
            customAlert("A senha deve ter no mínimo 8 caracteres.", "Atenção", "warning");
            return;
        }

        if (senha !== confirmarSenha) {
            customAlert("As senhas não coincidem.", "Atenção", "warning");
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            customAlert("E-mail inválido.", "Erro de Validação", "error");
            return;
        }

        const cpfRegex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
        if (!cpfRegex.test(cpf)) {
            customAlert("CPF inválido. Use o formato 000.000.000-00.", "Erro de Formato", "error");
            return;
        }

        try {
            botaoCadastrar.disabled = true;

            // Verifica duplicidade
            const respostaVerificacao = await fetch("../../../view/pages/forms_cadastro/3-verificar_cadastro.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, cpf, usuario }),
            });

            if (!respostaVerificacao.ok) {
                throw new Error('Erro na verificação');
            }

            const dadosVerificacao = await respostaVerificacao.json();

            if (dadosVerificacao.emailExiste) {
                customAlert("Este e-mail já está cadastrado.", "Cadastro Existente", "error");
                botaoCadastrar.disabled = false;
                return;
            }
            if (dadosVerificacao.cpfExiste) {
                customAlert("Este CPF já está cadastrado.", "Cadastro Existente", "error");
                botaoCadastrar.disabled = false;
                return;
            }
            if (dadosVerificacao.usuarioExiste) {
                customAlert("Este nome de usuário já está em uso.", "Cadastro Existente", "error");
                botaoCadastrar.disabled = false;
                return;
            }

            // Cadastro
            const respostaCadastro = await fetch("../../../view/pages/forms_cadastro/2-cadastrar_usuario.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    nome,
                    email,
                    usuario,
                    cpf,
                    senha,
                    escolaridade,
                    data_nascimento: dataNascimento
                }),
            });

            if (!respostaCadastro.ok) {
                throw new Error('Erro no cadastro');
            }

            const dadosCadastro = await respostaCadastro.json();

            if (dadosCadastro.sucesso) {
                customAlert("Cadastro realizado com sucesso!", "Sucesso", "success");
                window.location.href = "../../../view/pages/forms_login/1-forms_login.html";
            } else {
                customAlert(dadosCadastro.mensagem || "Erro ao cadastrar. Tente novamente.", "Erro no Cadastro", "error");
                botaoCadastrar.disabled = false;
            }

        } catch (error) {
            console.error("Erro na requisição:", error);
            customAlert("Erro inesperado. Tente novamente mais tarde.", "Erro", "error");
            botaoCadastrar.disabled = false;
        }
    });
});
