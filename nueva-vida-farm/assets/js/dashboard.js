/* <!--===============================================================================================--> */
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
/* <!--===============================================================================================--> */
const menuButton = document.querySelector("#menuButton");
const sidebar = document.querySelector("#sidebar");

menuButton.addEventListener("click", () => {
  if (window.innerWidth > 568) {
    // If window width is greater than 568, toggle d-md-flex
    sidebar.classList.toggle("d-md-flex");
  } else {
    // If window width is less than or equal to 568, toggle d-none and d-flex
    sidebar.classList.toggle("d-none");
    sidebar.classList.toggle("d-flex");
  }
});

/* <!--===============================================================================================--> */
const backButton = document.querySelector(".backButton");

backButton.addEventListener("click", () => {
  sidebar.classList.remove("d-flex");
  sidebar.classList.remove("d-none");
  sidebar.classList.add("d-none");
});
/* <!--===============================================================================================--> */