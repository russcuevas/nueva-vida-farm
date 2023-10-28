const profileButton = document.querySelector("#profileButton");
const profileDropdown = document.querySelector("#profileDropdown");

profileButton.addEventListener("click", () => {
  if (profileDropdown.classList.contains("d-none")) {
    profileDropdown.classList.remove("d-none");
    profileDropdown.classList.add("d-flex");
  } else {
    profileDropdown.classList.remove("d-flex");
    profileDropdown.classList.add("d-none");
  }
});

const cart = document.querySelector(".bi-bag");
const cartBadge = document.querySelector(".bi-bag span");
cartBadge.style.transition = 'color 0.2s ease-in';

cart.addEventListener("mouseenter", () => {
  cartBadge.style.color = "#049547";
});

cart.addEventListener("mouseleave", () => {
  cartBadge.style.color = "red";
});
