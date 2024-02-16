

const questionCountContainer = document.querySelector(".questions-count");
const questionNumber = document.getElementById("questions-number");
const answeredQuestions = document.getElementById("answered-questions");
const answerQuestions = document.querySelector(".answered-questions p");
const skip = document.querySelector(".skip-container");
const nextBtn = document.querySelector(".next-btn");
const resultBtn = document.querySelector(".result-btn");
const questionText = document.querySelector(".question-text");
const answerContainers = document.querySelectorAll(".answer-container");
const modal = document.querySelector(".modal");
const resultText = document.querySelector(".result-text");
const correctResult = document.querySelector(".correct-result");
const wrongResult = document.querySelector(".wrong-result");
const remainingResult = document.querySelector(".remaining-result");
const introCardContainer = document.querySelector(".intro-card-container");
const container = document.querySelector(".container");

let correctAnswers = 0;
let wrongAnswers = 0;
let remainingAnswers = 0;
let currentQuestion = 0;
// let questionsNumber = questions.length;
// questionNumber.innerText = questions.length;

const questionColors = [];

// for (let i = 0; i < questions.length; i++) {
//   questionColors.push("#bf97d8");
// }

// for (let i = 0; i < questions.length; i++) {
//   const questionCountElement = document.createElement("div");
//   questionCountElement.classList.add("question-count");
//   questionCountContainer.appendChild(questionCountElement);
// }

// const questionCount = document.querySelectorAll(".question-count");
// questionCount[currentQuestion].style.backgroundColor =
//   questionColors[currentQuestion];
// questionText.innerText = questions[currentQuestion].question;

// nextBtn.style.display = "none";

// let shuffledAnswers = [];
// let answerSelected = false;

function shuffleAnswers(correctAnswer, falseAnswers) {
  const allAnswers = [correctAnswer, ...falseAnswers];

  for (let i = allAnswers.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [allAnswers[i], allAnswers[j]] = [allAnswers[j], allAnswers[i]];
  }

  return allAnswers;
}

function handleAnswer(selectedAnswer) {
  const correctAnswer = questions[currentQuestion].correctAnswer;

  answerContainers.forEach((container) => {
    const answerText = container.innerText;

    if (answerText === selectedAnswer) {
      container.style.backgroundColor =
        selectedAnswer === correctAnswer ? "#F1F9EB" : "#FEECEC";
      container.style.border =
        selectedAnswer === correctAnswer
          ? "1px solid #88CE57"
          : "1px solid #F86363";
    } else {
      container.style.backgroundColor = "#ffffff";
      container.style.border = "none";
    }
  });

  if (selectedAnswer === correctAnswer) {
    correctAnswers++;
    correctResult.textContent = correctAnswers;
  } else {
    wrongAnswers++;
    wrongResult.textContent = wrongAnswers;

    answerContainers.forEach((container) => {
      const answerText = container.innerText;
      if (answerText === correctAnswer) {
        container.style.backgroundColor = "#F1F9EB";
        container.style.border = "1px solid #88CE57";
      }
    });
  }

  questionCount[currentQuestion].style.backgroundColor =
    selectedAnswer === correctAnswer ? "#88CE57" : "#F86363";
  questionColors[currentQuestion] =
    selectedAnswer === correctAnswer ? "#88CE57" : "#F86363";

  if (currentQuestion === questions.length - 1) {
    resultBtn.style.display = "block";
    skip.style.display = "none";
  } else {
    nextBtn.style.display = "block";
    skip.style.display = "none";
  }
}

// function displayQuestions() {
//   if (currentQuestion >= questions.length) {
//     return;
//   }

//   const correctAnswer = questions[currentQuestion].correctAnswer;
//   const falseAnswers = questions[currentQuestion].answers.filter(
//     (answer) => answer !== correctAnswer
//   );

//   shuffledAnswers = shuffleAnswers(correctAnswer, falseAnswers);

//   answerContainers.forEach((label, index) => {
//     label.innerText = shuffledAnswers[index];
//   });

//   questionText.innerText = questions[currentQuestion].question;
//   questionCount[currentQuestion].style.backgroundColor =
//     questionColors[currentQuestion];
// }

answerContainers.forEach((answerContainer, index) => {
  answerContainer.addEventListener("click", () => {
    if (!answerSelected) {
      answerSelected = true;
      const selectedAnswer = answerContainers[index].innerText;

      handleAnswer(selectedAnswer);
    }
  });
});

skip.addEventListener("click", () => {
  if (!answerSelected) {
    answerSelected = true;

    questionCount[currentQuestion].style.backgroundColor = "#6766661F";
    remainingAnswers++;
    remainingResult.textContent = remainingAnswers;

    answerContainers.forEach((container) => {
      container.style.backgroundColor = "#ffffff";
      container.style.border = "none";
    });

    answerSelected = false;

    currentQuestion++;

    if (currentQuestion >= questions.length) {
      resultBtn.style.display = "block";
      skip.style.display = "none";
    }

    answeredQuestions.textContent =
      currentQuestion === questions.length
        ? questions.length
        : currentQuestion + 1;
    displayQuestions();
  }
});

function handleResultBtn() {
  if (currentQuestion === questions.length - 1) {
    nextBtn.style.display = "none";
    skip.style.display = "none";
    resultBtn.style.display = "block";
  } else {
    resultBtn.style.display = "none";
  }
}

nextBtn.addEventListener("click", () => {
  answerContainers.forEach((container) => {
    container.style.backgroundColor = "#ffffff";
    container.style.border = "none";
  });

  answerSelected = false;
  currentQuestion++;

  if (currentQuestion < questions.length) {
    skip.style.display = "block";
    answeredQuestions.textContent = currentQuestion + 1;
    displayQuestions();
  } else {
    currentQuestion = 0;
    skip.style.display = "none";
    resultBtn.style.display = "block";
  }

  nextBtn.style.display = "none";
});

resultBtn.addEventListener("click", () => {
  let questionsAnswered = questions.length - remainingAnswers;
  wrongResult.textContent = wrongAnswers;
  correctResult.textContent = correctAnswers;
  remainingResult.textContent = remainingAnswers;
  modal.style.display = "flex";
  answerQuestions.textContent = `You answered ${questionsAnswered} from ${questionsNumber} questions`;
  if (correctAnswers > wrongAnswers && remainingAnswers) {
    resultText.textContent = "Fantastic work! Your knowledge shines bright";
  } else {
    resultText.textContent =
      "Every challenge is a step towards improvement... You've got this!";
  }
  remainingResult.textContent = remainingAnswers;
  modal.style.background = "rgba(0, 0, 0, 0.2)";
  modal.style.zIndex = "1000";
  container.style.zIndex = "-1000";
});

// displayQuestions();
