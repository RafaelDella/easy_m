document.getElementById('modoQuitacao').addEventListener('change', function () {
  const modo = this.value;
  document.getElementById('campoValorParcela').classList.toggle('hidden', modo !== 'valor_parcela');
  document.getElementById('campoTempo').classList.toggle('hidden', modo !== 'tempo_quitacao');
});

document.getElementById('debtForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const valorTotal = parseFloat(document.getElementById('valorTotal').value);
  const juros = parseFloat(document.getElementById('juros').value);
  const tipoJuros = document.getElementById('tipoJuros').value;
  const modo = document.ElementById('modoQuitacao').value;

  let tempoQuitacao = 0;

  if (modo === 'valor_parcela') {
    const parcela = parseFloat(document.getElementById('valorParcela').value);
    let taxaMensal = tipoJuros === 'anual' ? juros / 12 / 100 : juros / 100;
    tempoQuitacao = Math.ceil(Math.log((parcela / (parcela - valorTotal * taxaMensal))) / Math.log(1 + taxaMensal));
  } else {
    let tempo = parseFloat(document.getElementById('tempo').value);
    const unidade = document.getElementById('unidadeTempo').value;
    tempoQuitacao = unidade === 'anos' ? tempo * 12 : tempo;
  }

  const resultadoTexto = `A previsão de quitação é de aproximadamente ${tempoQuitacao} meses.`;
  document.getElementById('resultadoTexto').textContent = resultadoTexto;
  document.getElementById('popupResultado').classList.remove('hidden');
});

// Botão fechar popup
document.getElementById('fecharPopup').addEventListener('click', function () {
  document.getElementById('popupResultado').classList.add('hidden');
});

// Recalcular
document.getElementById('recalcularBtn').addEventListener('click', function () {
  document.getElementById('popupResultado').classList.add('hidden');
  document.getElementById('valorParcela').value = '';
  document.getElementById('tempo').value = '';
});

// Simular "salvar" no banco
document.getElementById('salvarBtn').addEventListener('click', function () {
  alert("Previsão salva com sucesso! (simulado)");
  document.getElementById('popupResultado').classList.add('hidden');
});
