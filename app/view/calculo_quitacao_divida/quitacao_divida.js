document.addEventListener('DOMContentLoaded', function () {
  const modoQuitacao = document.getElementById('modoQuitacao');
  const campoValorParcela = document.getElementById('campoValorParcela');
  const campoTempo = document.getElementById('campoTempo');

  // Alterna exibição dos campos
  modoQuitacao.addEventListener('change', function () {
    if (this.value === 'valor_parcela') {
      campoValorParcela.classList.remove('hidden');
      campoTempo.classList.add('hidden');
    } else {
      campoValorParcela.classList.add('hidden');
      campoTempo.classList.remove('hidden');
    }
  });

  const debtForm = document.getElementById('debtForm');
  const popup = document.getElementById('popupResultado');
  const fecharPopup = document.getElementById('fecharPopup');
  const recalcularBtn = document.getElementById('recalcularBtn');
  const resultadoTexto = document.getElementById('resultadoTexto');

  debtForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const tipoDivida = document.getElementById('tipoDivida').value;
    const valorTotal = parseFloat(document.getElementById('valorTotal').value);
    const juros = parseFloat(document.getElementById('juros').value);

    // Verificações iniciais
    if (isNaN(valorTotal) || valorTotal <= 0 || isNaN(juros) || juros < 0) {
      alert("Preencha corretamente os valores da dívida e dos juros.");
      return;
    }

    let resultado = `Para sua dívida de <strong>${tipoDivida}</strong> no valor de <strong>${valorTotal.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</strong> com juros de <strong>${juros}%</strong>:`;

    if (modoQuitacao.value === 'valor_parcela') {
      const valorParcela = parseFloat(document.getElementById('valorParcela').value);
      if (isNaN(valorParcela) || valorParcela <= 0) {
        alert("Informe um valor válido para a parcela.");
        return;
      }
      const meses = Math.ceil((valorTotal * (1 + juros / 100)) / valorParcela);
      resultado += `<br><br>Pagando <strong>${valorParcela.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</strong> por mês, você quitará em aproximadamente <strong>${meses} meses</strong>.`;

    } else {
      const tempo = parseFloat(document.getElementById('tempo').value);
      const unidade = document.getElementById('unidadeTempo').value;
      if (isNaN(tempo) || tempo <= 0) {
        alert("Informe um tempo válido para quitação.");
        return;
      }
      const mesesTotal = unidade === 'anos' ? tempo * 12 : tempo;
      const parcela = (valorTotal * (1 + juros / 100)) / mesesTotal;
      resultado += `<br><br>Para quitar em <strong>${tempo} ${unidade}</strong>, você precisará pagar aproximadamente <strong>${parcela.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</strong> por mês.`;
    }

    resultadoTexto.innerHTML = resultado;
    popup.classList.add('active');
  });

  fecharPopup.addEventListener('click', function () {
    popup.classList.remove('active');
  });

  recalcularBtn.addEventListener('click', function () {
    popup.classList.remove('active');
    debtForm.reset();
    campoValorParcela.classList.remove('hidden');
    campoTempo.classList.add('hidden');
  });

  popup.addEventListener('click', function (e) {
    if (e.target === popup) {
      popup.classList.remove('active');
    }
  });
});
