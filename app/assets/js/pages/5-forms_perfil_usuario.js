document.addEventListener("DOMContentLoaded", async () => {
    try {
        const resposta = await fetch("2-carregar_perfil_usuario.php");
        const dados = await resposta.json();

        if (dados.sucesso) {
            const u = dados.usuario;
            document.getElementById("usuario_id").value = u.id;
            document.getElementById("nome").value = u.nome;
            document.getElementById("email").value = u.email;
            document.getElementById("usuario").value = u.usuario;
            document.getElementById("cpf").value = u.cpf;
            document.getElementById("escolaridade").value = u.escolaridade;
            document.getElementById("data_nascimento").value = u.data_nascimento;
        } else {
            alert("❌ Não foi possível carregar os dados do perfil.");
        }
    } catch (err) {
        alert("❌ Erro ao carregar dados do perfil.");
        console.error(err);
    }
});

function confirmarExclusao() {
    if (confirm("⚠️ Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.")) {
        window.location.href = "4-excluir_perfil_usuario.php";
    }
}
