const startBtn = document.querySelector(".start-btn");
const introCard = document.querySelector(".intro-card");
const closeIcons = document.querySelectorAll(".close-icon");
const viewIcons = document.querySelectorAll(".view-icon");
const trashIcons = document.querySelectorAll(".trash-icon");
const introCardContainer = document.querySelector(".intro-card-container");
const questionModalContainer = document.querySelector(
  ".questions-modal-container"
);
const container = document.querySelector(".container");

viewIcons.forEach((viewIcon) => {
  viewIcon.addEventListener("click", () => {
    questionModalContainer.style.display = "flex";
    questionModalContainer.style.background = "rgba(0, 0, 0, 0.2)";
    questionModalContainer.style.zIndex = "1000";
  });
});
closeIcons.forEach((closeIcon) => {
  closeIcon.addEventListener("click", () => {
    introCard.style.display = "none";
    introCardContainer.style.background = "";
    introCardContainer.style.zIndex = "-1000";
    questionModalContainer.style.display = "none";
    questionModalContainer.style.background = "";
    questionModalContainer.style.zIndex = "-1000";
  });
});

const uploadBtn = document.getElementById("upload-btn");
const fileUpload = document.getElementById("file-upload");

if (uploadBtn) {
  uploadBtn.addEventListener("click", function () {
    fileUpload.click();
  });
}

