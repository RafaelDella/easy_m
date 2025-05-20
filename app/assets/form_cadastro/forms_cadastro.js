document.addEventListener("DOMContentLoaded", function () {
    const formulario = document.getElementById("formularioCadastro");

    formulario.addEventListener("submit", async function (e) {
        e.preventDefault(); // impede envio tradicional

        const nomeInput = document.getElementById("nome");
        const emailInput = document.getElementById("email");
        const usuarioInput = document.getElementById("usuario");
        const cpfInput = document.getElementById("cpf");
        const senhaInput = document.getElementById("senha");
        const confirmarSenhaInput = document.getElementById("confirmar_senha");
        const escolaridadeInput = document.getElementById("escolaridade");
        const dataNascimentoInput = document.getElementById("data_nascimento");

        let nome = nomeInput.value.trim();
        const email = emailInput.value.trim();
        const usuario = usuarioInput.value.trim();
        const cpf = cpfInput.value.trim();
        const senha = senhaInput.value;
        const confirmarSenha = confirmarSenhaInput.value;
        const escolaridade = escolaridadeInput.value;
        const dataNascimento = dataNascimentoInput.value;

        // Capitaliza o nome
        nome = nome.charAt(0).toUpperCase() + nome.slice(1).toLowerCase();
        nomeInput.value = nome;

        // Validações básicas
        if (!nome || !email || !usuario || !cpf || !senha || !confirmarSenha || !escolaridade || !dataNascimento) {
            alert("Todos os campos são obrigatórios.");
            return;
        }

        if (senha.length < 8) {
            alert("A senha deve ter no mínimo 8 caracteres.");
            return;
        }

        if (senha !== confirmarSenha) {
            alert("As senhas não coincidem.");
            return;
        }

        // Validação de e-mail
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("E-mail inválido.");
            return;
        }

        try {
            // 1º Passo: Verificar se já existe email, cpf ou usuario
            const respostaVerificacao = await fetch("../../view/formularioulario_cadastro/verifica_usuario.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ email, cpf, usuario }),
            });

            const dadosVerificacao = await respostaVerificacao.json();

            if (dadosVerificacao.emailExiste) {
                alert("Este e-mail já está cadastrado.");
                return;
            }
            if (dadosVerificacao.cpfExiste) {
                alert("Este CPF já está cadastrado.");
                return;
            }
            if (dadosVerificacao.usuarioExiste) {
                alert("Este nome de usuário já está em uso.");
                return;
            }

            // 2º Passo: Enviar cadastro via AJAX
            const respostaCadastro = await fetch("../../view/formularioulario_cadastro/cadastrar_usuario.php", {
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

            const dadosCadastro = await respostaCadastro.json();

            if (dadosCadastro.sucesso) {
                alert("✅ Cadastro realizado com sucesso!");
                window.location.href = "../../view/formularioulario_login/formulario_login.html"; // redireciona para login
            } else {
                alert("❌ Erro ao cadastrar: " + (dadosCadastro.mensagem || "Tente novamente."));
            }

        } catch (error) {
            console.error("Erro na requisição:", error);
            alert("Erro inesperado. Tente novamente mais tarde.");
        }
    });
});
