document.addEventListener("DOMContentLoaded", function () {
    const forms = document.getElementById("formsGasto");
  
    forms.addEventListener("submit", function (event) {
      event.preventDefault(); // Impede o envio padrão
  
      const nome = document.getElementById("nome_gasto").value.trim();
      const valor = parseFloat(document.getElementById("valor_gasto").value);
      const data = document.getElementById("data_gasto").value;
  
      if (!nome || isNaN(valor) || !data) {
        alert("Por favor, preencha os campos obrigatórios corretamente!");
        return;
      }
  
      // Confirmação
      const confirmar = confirm("Deseja realmente cadastrar esse gasto?");
      if (!confirmar) return;
  
      // Envia para o PHP
      fetch("processa_gasto.php", {
        method: "POST",
        body: new formsData(forms),
      })
        .then((res) => res.text())
        .then((resposta) => {
          alert(resposta);
          forms.reset();
        })
        .catch((erro) => {
          alert("Erro ao enviar gasto. Tente novamente.");
          console.error(erro);
        });
    });
  });
  