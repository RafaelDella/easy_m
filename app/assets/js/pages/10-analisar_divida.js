document.addEventListener('DOMContentLoaded', function () {
    const categorias = {};
    document.querySelectorAll('table tbody tr').forEach(tr => {
        const categoria = tr.children[1].innerText.trim();
        const valor = parseFloat(tr.children[2].innerText.replace('R$', '').replace('.', '').replace(',', '.'));
        categorias[categoria] = (categorias[categoria] || 0) + valor;
    });

    const ctx = document.getElementById('graficoCategorias').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: Object.keys(categorias),
            datasets: [{
                label: 'Total por Categoria',
                data: Object.values(categorias),
                backgroundColor: ['#4caf50', '#ff9800', '#03a9f4', '#f44336', '#9c27b0', '#00bcd4']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});

function simular() {
    const valor = parseFloat(document.getElementById('valorParcela').value);
    const prazo = parseInt(document.getElementById('prazoMeses').value);
    const resultado = document.getElementById('resultadoSimulacao');

    if (!valor && !prazo) {
        resultado.innerHTML = '<p style="color:orange">Informe o valor mensal ou o prazo.</p>';
        return;
    }

    const totalDivida = [...document.querySelectorAll('table tbody tr')]
        .map(tr => parseFloat(tr.children[2].innerText.replace('R$', '').replace('.', '').replace(',', '.')))
        .reduce((acc, v) => acc + v, 0);

    let simulacao = '';
    let mensal = 0;

    if (valor) {
        const meses = Math.ceil(totalDivida / valor);
        simulacao += `<p>Com R$ ${valor.toFixed(2)} por mês, você quitará em aproximadamente ${meses} meses.</p>`;
    }

    if (prazo) {
        mensal = totalDivida / prazo;
        simulacao += `<p>Para quitar em ${prazo} meses, será necessário pagar R$ ${mensal.toFixed(2)} por mês.</p>`;
    }

    resultado.innerHTML = simulacao;

    const dica = document.getElementById('mensagemDica');
    if (mensal > 0 && valor && mensal > valor) {
        dica.innerText = "Considere aumentar sua parcela mensal ou alongar o prazo.";
    } else if (mensal > 0 || valor > 0) {
        dica.innerText = "Você está no caminho certo para quitar suas dívidas!";
    } else {
        dica.innerText = "";
    }
}
