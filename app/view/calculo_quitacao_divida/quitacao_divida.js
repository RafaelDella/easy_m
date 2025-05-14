document.addEventListener('DOMContentLoaded', function() {
  const modoQuitacao = document.getElementById('modoQuitacao');
  const campoValorParcela = document.getElementById('campoValorParcela');
  const campoTempo = document.getElementById('campoTempo');
  
  // Mostrar campos conforme seleção
  modoQuitacao.addEventListener('change', function() {
    if (this.value === 'valor_parcela') {
      campoValorParcela.classList.remove('hidden');
      campoTempo.classList.add('hidden');
    } else {
      campoValorParcela.classList.add('hidden');
      campoTempo.classList.remove('hidden');
    }
  });
  
  // Form submission
  const debtForm = document.getElementById('debtForm');
  const popup = document.getElementById('popupResultado');
  const fecharPopup = document.getElementById('fecharPopup');
  const recalcularBtn = document.getElementById('recalcularBtn');
  
  debtForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Simulação de cálculo (substitua pela lógica real)
    const tipoDivida = document.getElementById('tipoDivida').value;
    const valorTotal = parseFloat(document.getElementById('valorTotal').value);
    const juros = parseFloat(document.getElementById('juros').value);
    
    let resultado = `Para sua dívida de ${tipoDivida} no valor de R$ ${valorTotal.toFixed(2)} com juros de ${juros}%:`;
    
    if (modoQuitacao.value === 'valor_parcela') {
      const valorParcela = parseFloat(document.getElementById('valorParcela').value);
      const meses = Math.ceil(valorTotal / valorParcela);
      resultado += `<br><br>Pagando R$ ${valorParcela.toFixed(2)} por mês, você quitará em aproximadamente ${meses} meses.`;
    } else {
      const tempo = parseFloat(document.getElementById('tempo').value);
      const unidade = document.getElementById('unidadeTempo').value;
      const mesesTotal = unidade === 'anos' ? tempo * 12 : tempo;
      const parcela = (valorTotal * (1 + juros/100)) / mesesTotal;
      resultado += `<br><br>Para quitar em ${tempo} ${unidade}, você precisará pagar aproximadamente R$ ${parcela.toFixed(2)} por mês.`;
    }
    
    document.getElementById('resultadoTexto').innerHTML = resultado;
    popup.classList.add('active');
  });
  
  fecharPopup.addEventListener('click', function() {
    popup.classList.remove('active');
  });
  
  recalcularBtn.addEventListener('click', function() {
    popup.classList.remove('active');
  });
  
  // Fechar popup ao clicar fora
  popup.addEventListener('click', function(e) {
    if (e.target === popup) {
      popup.classList.remove('active');
    }
  });
});