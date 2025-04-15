
const steps = document.querySelectorAll(".form-step");
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
const submitBtn = document.getElementById("submitBtn");

let currentStep = 0;

function showStep(step) {
  steps.forEach((el, i) => {
    el.classList.toggle("active", i === step);
  });
  prevBtn.style.display = step > 0 ? "inline-block" : "none";
  nextBtn.style.display = step < steps.length - 1 ? "inline-block" : "none";
  submitBtn.style.display = step === steps.length - 1 ? "inline-block" : "none";
}

nextBtn.addEventListener("click", () => {
  const radios = steps[currentStep].querySelectorAll("input[type='radio']");
  const nameSet = new Set();
  let allAnswered = true;

  radios.forEach((radio) => {
    if (!nameSet.has(radio.name)) {
      nameSet.add(radio.name);
      const groupChecked = steps[currentStep].querySelector(`input[name="${radio.name}"]:checked`);
      if (!groupChecked) allAnswered = false;
    }
  });

  if (!allAnswered) return alert("Por favor, selecione uma opção.");

  currentStep++;
  showStep(currentStep);
});

prevBtn.addEventListener("click", () => {
  currentStep--;
  showStep(currentStep);
});

showStep(currentStep);
