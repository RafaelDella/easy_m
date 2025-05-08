document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("formLogin");
    const msgErro = document.getElementById("mensagem-erro");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        msgErro.textContent = "";

        const usuario = document.getElementById("usuario").value.trim();
        const senha = document.getElementById("senha").value;

        const resposta = await fetch("login_usuario.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ usuario, senha }),
        });

        const dados = await resposta.json();

        if (dados.sucesso) {
            window.location.href = "../dashboard.php";
        } else {
            msgErro.textContent = dados.mensagem || "Erro ao fazer login.";
        }
    });
});
