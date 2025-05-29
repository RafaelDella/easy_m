console.log('Modal disponível?', typeof customAlert); // Deve retornar "function"
if (typeof customAlert !== 'function') {
    console.error('Modal não carregou! Verifique a ordem dos scripts.');
}

document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formLogin");
    const msgErro = document.getElementById("mensagem-erro");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        msgErro.textContent = "";

        const usuario = document.getElementById("usuario").value.trim();
        const senha = document.getElementById("senha").value;

        if (!usuario || !senha) {
            customAlert("Preencha todos os campos.", "Atenção", "warning");
            return;
        }

        try {
            const resposta = await fetch("../../../view/pages/forms_login/2-login_usuario.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ usuario, senha }),
            });

            if (!resposta.ok) throw new Error("Erro na requisição");

            const dados = await resposta.json();

            if (dados.sucesso) {
                window.location.href = "../dashboard/1-dashboard.php";
            } else {
                customAlert(dados.mensagem || "Erro ao fazer login.", "Erro", "error");
            }
        } catch (err) {
            msgErro.textContent = "Erro ao conectar com o servidor.";
            customAlert("Erro ao conectar com o servidor.", "Erro", "error");
        }
    });
});
