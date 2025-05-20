function validarformularioulario() {
    const descricao = document.getElementById('descricao').value.trim();
    const valor = parseFloat(document.getElementById('valor').value);
    const categoria = document.getElementById('categoria').value;
    const data = document.getElementById('data_entrada').value;

    if (!descricao || !categoria || !data || isNaN(valor) || valor <= 0) {
        alert("Por favor, preencha todos os campos corretamente!");
        return false;
    }
    return true;
}
