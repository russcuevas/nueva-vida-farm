const backButton = document.querySelector("#backButton");
const backText = document.querySelector("#backText");

backButton.addEventListener("mouseenter", () => {
  backButton.style.color = "#049547";
  backText.style.color = "#049547";
});

backButton.addEventListener("mouseleave", () => {
  backButton.style.color = "black";
  backText.style.color = "black";
});

backText.addEventListener("mouseenter", () => {
  backButton.style.color = "#049547";
  backText.style.color = "#049547";
});

backText.addEventListener("mouseleave", () => {
  backButton.style.color = "black";
  backText.style.color = "black";
});
