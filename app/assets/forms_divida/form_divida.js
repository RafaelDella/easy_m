function validateforms() {
    const inputs = document.querySelectorAll("forms input");
    for (const input of inputs) {
      if (!input.value) {
        showModal("Por favor, preencha todos os campos.");
        return false;
      }
    }
    return true;
  }
  
  function showModal(message) {
    document.getElementById("modal-message").textContent = message;
    document.getElementById("modal").style.display = "block";
  }
  
  function closeModal() {
    document.getElementById("modal").style.display = "none";
  }
  