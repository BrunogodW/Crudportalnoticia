// Elementos do menu
const openBtn = document.getElementById("openMenu");
const closeBtn = document.getElementById("closeMenu");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const navLinks = document.querySelectorAll(".nav-link");

// Função para abrir menu
function openMenu() {
  sidebar.classList.add("active");
  overlay.classList.add("active");
  document.body.style.overflow = "hidden";
}

// Função para fechar menu
function closeMenu() {
  sidebar.classList.remove("active");
  overlay.classList.remove("active");
  document.body.style.overflow = "auto";
}

// Event listeners
openBtn.addEventListener("click", openMenu);
closeBtn.addEventListener("click", closeMenu);
overlay.addEventListener("click", closeMenu);

// Fechar menu ao clicar em um link
navLinks.forEach(link => {
  link.addEventListener("click", (e) => {
    // Se não for um link de âncora vazio, fecha o menu
    if (link.getAttribute("href") !== "#") {
      closeMenu();
    } else {
      e.preventDefault();
    }
  });
});
    