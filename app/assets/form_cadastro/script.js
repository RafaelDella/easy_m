document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formCadastro");

    form.addEventListener("submit", async function (e) {
        e.preventDefault(); // impede envio tradicional

        const nomeInput = document.getElementById("nome");
        const emailInput = document.getElementById("email");
        const usuarioInput = document.getElementById("usuario");
        const cpfInput = document.getElementById("cpf");
        const senhaInput = document.getElementById("senha");

        let nome = nomeInput.value.trim();
        const email = emailInput.value.trim();
        const usuario = usuarioInput.value.trim();
        const cpf = cpfInput.value.trim();
        const senha = senhaInput.value;

        // Capitalize automático no nome
        nome = nome.charAt(0).toUpperCase() + nome.slice(1).toLowerCase();
        nomeInput.value = nome;

        // Validações básicas
        if (!nome || !email || !usuario || !cpf || !senha) {
            alert("Todos os campos são obrigatórios.");
            return;
        }

        if (senha.length < 8) {
            alert("A senha deve ter no mínimo 8 caracteres.");
            return;
        }

        // Validação de e-mail
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert("E-mail inválido.");
            return;
        }

        // Verificação via AJAX
        const resposta = await fetch("verifica_usuario.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ email, cpf, usuario }),
        });

        const dados = await resposta.json();

        if (dados.emailExiste) {
            alert("Este e-mail já está cadastrado.");
            return;
        }

        if (dados.cpfExiste) {
            alert("Este CPF já está cadastrado.");
            return;
        }

        if (dados.usuarioExiste) {
            alert("Este nome de usuário já está em uso.");
            return;
        }

        // Tudo certo: envia o formulário
        form.submit();
    });
});
