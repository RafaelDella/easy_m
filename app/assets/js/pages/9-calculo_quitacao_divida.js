document.addEventListener('DOMContentLoaded', function () {
    const selectDivida = document.getElementById('dividaSelecionada');
    const resultadoTexto = document.getElementById('resultadoTexto');
    const popup = document.getElementById('popupResultado');
    const fecharPopup = document.getElementById('fecharPopup');
    const salvarBtn = document.getElementById('salvarBtn');
    const recalcularBtn = document.getElementById('recalcularBtn');

    const campoValorParcela = document.getElementById('campoValorParcela');
    const campoTempo = document.getElementById('campoTempo');
    const modoQuitacao = document.getElementById('modoQuitacao');

    const tipoDividaEl = document.getElementById('tipoDivida');
    const valorTotalEl = document.getElementById('valorTotal');
    const jurosEl = document.getElementById('juros');
    const valorPagoEl = document.getElementById('valorPago');

    let nomeDividaSelecionada = '';

    // Alternar campos com base no modo de quitação
    modoQuitacao.addEventListener('change', function () {
        campoValorParcela.classList.toggle('hidden', this.value !== 'valor_parcela');
        campoTempo.classList.toggle('hidden', this.value !== 'tempo_quitacao');
    });

    // Quando uma dívida é selecionada
    if (selectDivida) {
        selectDivida.addEventListener('change', () => {
            const dados = selectDivida.value;

            if (!dados) {
                nomeDividaSelecionada = '';
                tipoDividaEl.value = '';
                valorTotalEl.value = '';
                jurosEl.value = '';
                valorPagoEl.value = '';

                tipoDividaEl.disabled = false;
                valorTotalEl.readOnly = false;
                valorPagoEl.readOnly = false;
                jurosEl.readOnly = false;
                return;
            }

            try {
                const divida = JSON.parse(dados);
                const categoria = divida.categoria_divida || '';

                valorTotalEl.value = divida.valor_total || '';
                jurosEl.value = divida.taxa_divida || '';
                valorPagoEl.value = parseFloat(divida.valor_pago || 0).toFixed(2);
                nomeDividaSelecionada = divida.nome_divida?.trim() || 'Dívida sem nome';

                // Se a categoria não estiver nas opções do select, adiciona
                if (![...tipoDividaEl.options].some(opt => opt.value === categoria)) {
                    const novaOption = document.createElement('option');
                    novaOption.value = categoria;
                    novaOption.textContent = categoria;
                    tipoDividaEl.appendChild(novaOption);
                }

                tipoDividaEl.value = categoria;
                tipoDividaEl.disabled = true;
                valorTotalEl.readOnly = true;
                jurosEl.readOnly = false;
                valorPagoEl.readOnly = false; // ✅ Permitir edição mesmo com dívida selecionada
            } catch (e) {
                console.warn('Erro ao carregar dívida:', e);
            }
        });
    }

    if (fecharPopup) {
        fecharPopup.addEventListener('click', () => popup.classList.add('hidden'));
    }

    if (recalcularBtn) {
        recalcularBtn.addEventListener('click', () => popup.classList.add('hidden'));
    }

    document.getElementById('debtForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const valorTotal = parseFloat(valorTotalEl.value);
        const juros = parseFloat(jurosEl.value);
        const tipoJuros = document.getElementById('tipoJuros').value;
        const modo = modoQuitacao.value;

        // Atualiza o valorPago com o valor do input (mesmo que editado manualmente)
        const valorPago = parseFloat(valorPagoEl.value) || 0;

        if (isNaN(valorTotal) || isNaN(juros) || valorTotal <= 0 || juros < 0) {
            resultadoTexto.innerHTML = "Preencha corretamente o valor da dívida e os juros.";
            popup.classList.remove('hidden');
            return;
        }

        const valorRestante = valorTotal - valorPago;
        if (valorRestante <= 0) {
            resultadoTexto.innerHTML = "Parabéns! Essa dívida já foi totalmente quitada.";
            popup.classList.remove('hidden');
            return;
        }

        const taxaMensal = tipoJuros === 'anual' ? juros / 12 / 100 : juros / 100;

        let parcela = 0, meses = 0, totalComJuros = 0;
        let resultado = '';

        if (modo === 'valor_parcela') {
            parcela = parseFloat(document.getElementById('valorParcela').value);
            if (!parcela || parcela <= 0) {
                resultadoTexto.innerHTML = "Informe um valor de parcela válido.";
                popup.classList.remove('hidden');
                return;
            }

            if (taxaMensal === 0) {
                meses = Math.ceil(valorRestante / parcela);
            } else {
                const denominador = parcela - taxaMensal * valorRestante;
                if (denominador <= 0) {
                    resultadoTexto.innerHTML = "O valor da parcela é muito baixo para cobrir os juros mensais.";
                    popup.classList.remove('hidden');
                    return;
                }

                meses = Math.ceil(Math.log(parcela / denominador) / Math.log(1 + taxaMensal));
            }

            totalComJuros = parcela * meses;
        } else {
            const tempo = parseInt(document.getElementById('tempo').value);
            const unidade = document.getElementById('unidadeTempo').value;
            meses = unidade === 'anos' ? tempo * 12 : tempo;

            if (!tempo || tempo <= 0) {
                resultadoTexto.innerHTML = "Informe um tempo válido para quitação.";
                popup.classList.remove('hidden');
                return;
            }

            parcela = (valorRestante * taxaMensal) / (1 - Math.pow(1 + taxaMensal, -meses));
            totalComJuros = parcela * meses;
        }

        resultado = `
            <strong>Dívida:</strong> ${nomeDividaSelecionada}<br><br>
            Valor já pago: <strong>R$ ${valorPago.toFixed(2)}</strong><br>
            Valor restante (sem juros): <strong>R$ ${valorRestante.toFixed(2)}</strong><br>
            Total a pagar (com juros): <strong>R$ ${totalComJuros.toFixed(2)}</strong><br>
            Total geral: <strong>R$ ${(totalComJuros + valorPago).toFixed(2)}</strong><br><br>
            ${modo === 'valor_parcela'
                ? `Você quitará a dívida em <strong>${meses} meses</strong>, pagando <strong>R$ ${parcela.toFixed(2)}</strong> por mês.`
                : `Você precisará pagar <strong>R$ ${parcela.toFixed(2)}</strong> por mês durante <strong>${meses} meses</strong>.`
            }
        `;

        resultadoTexto.innerHTML = resultado;
        popup.classList.remove('hidden');
    });
});
