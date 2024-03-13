<!DOCTYPE html>
<?php
require_once __DIR__ . '../../vendor/autoload.php';
session_start();
if (isset($_GET['data'])) {
  $flashcardsData = json_decode(urldecode($_GET['data']), true);
  $flashcards = json_encode($flashcardsData['success']);
}
$subject = $_GET['subject'];
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flashcards Quiz</title>
  <link href="./quiz.css" rel="stylesheet" />
</head>
<body>
<div class="container">
        <div>
            <div class="title-container">
                <h1 class="title">
                    <?php echo $subject; ?>
                </h1>
                <a href="/flashcard">
                    <img class="close-icon" src="../quizes/icons/close.svg" alt="" />
                </a>
            </div>
            <div class="modal">
                <div class="result-modal">
                    <h3 class="title">Result</h3>
                    <p class="result-text"></p>
                    <div class="result-container">
                        <div class="result-card">
                            <div class="result">
                                <div class="correct"></div>
                                <p class="mastered-result">20</p>
                            </div>
                            <p class="info">Mastered</p>
                        </div>

                        <div class="result-card">
                            <div class="result">
                                <div class="remaining"></div>
                                <p class="stillLearning-result">30</p>
                            </div>
                            <p class="info">Still Learning</p>
                        </div>
                    </div>
                    <div class="answered-questions">
                        <p>1</p>
                    </div>
                    <a href="/flashcard">
                        <button class="back-btn">Back to Home</button>
                    </a>
                </div>
            </div>
            <div class="questions-count"></div>
            <div class="question-container">
                <div class="question-progress">
                    <p>
                        <span id="answered-questions">1</span> from
                        <span id="questions-number">10</span>
                    </p>
                </div>
                <div class="answers-container">
                    <div class="flashcard">
                        <div class="card">
                            <div class="card-face front answer">Data Mining</div>
                            <div class="card-face back question-text">
                                Data Mining refers to the nontrivial extraction of implicit,
                                previously unknown, and potentially useful information from data in
                                databases.
                            </div>
                        </div>
                    </div>

                    <div class="btn-container">
                        <div class="skip-container">
                            <p class="skip-btn">Still Learning</p>
                        </div>
                        <div>
                            <button class="mastered-btn quiz-btn">Mastered</button>
                            <button class="result-btn quiz-btn">Result</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script>
      const questions =  <?php echo $flashcards; ?>;

const questionCountContainer = document.querySelector(".questions-count");
const questionNumber = document.getElementById("questions-number");
const answeredQuestions = document.getElementById("answered-questions");
const answerQuestions = document.querySelector(".answered-questions p");
const skip = document.querySelector(".skip-container");
const masteredBtn = document.querySelector(".mastered-btn");
const resultBtn = document.querySelector(".result-btn");
const questionText = document.querySelector(".question-text");
const answerContainers = document.querySelectorAll(".answer-container");
const answer = document.querySelectorAll(".answer");
const modal = document.querySelector(".modal");
const resultText = document.querySelector(".result-text");
const masteredResult = document.querySelector(".mastered-result");
const stillLearningResult = document.querySelector(".stillLearning-result");
const card = document.querySelector(".card");
const flashcards = document.querySelectorAll(".flashcard");

let currentQuestion = 0;
let masteredAnswers = 0;
let stillLearningAnswers = 0;
let questionsNumber = questions.length;
questionNumber.innerText = questions.length;

const questionColors = Array.from(
  { length: questions.length },
  () => "#4a154b"
);

questionColors.forEach((color) => {
  const questionCountElement = document.createElement("div");
  questionCountElement.classList.add("question-count");
  questionCountContainer.appendChild(questionCountElement);
});

const questionCount = document.querySelectorAll(".question-count");
questionCount[currentQuestion].style.backgroundColor =
  questionColors[currentQuestion];
questionText.innerText = questions[currentQuestion].content;

function displayQuestions() {
  if (currentQuestion >= questions.length) {
    return;
  }
  const questionTextElement = document.querySelector(".question-text");
  questionTextElement.innerText = questions[currentQuestion].content;

  const answerElement = document.querySelector(".answer");
  answerElement.innerText = questions[currentQuestion].answer;

  questionCount[currentQuestion].style.backgroundColor =
    questionColors[currentQuestion];
}

skip.addEventListener("click", () => {
  questionCount[currentQuestion].style.backgroundColor = "#6766661F";
  stillLearningAnswers++;
  stillLearningResult.textContent = stillLearningAnswers;
  masteredResult.textContent = masteredAnswers;

  answerContainers.forEach((container) => {
    container.style.backgroundColor = "#ffffff";
    container.style.border = "none";
  });

  currentQuestion++;

  if (currentQuestion >= questions.length) {
    resultBtn.style.display = "block";
    skip.style.display = "none";
    masteredBtn.style.display = "none";
    currentQuestion = questions.length - 1;
  }

  answeredQuestions.textContent = currentQuestion + 1;
  displayQuestions();
  card.classList.remove("is-flipped");
});

masteredBtn.addEventListener("click", () => {
  if (currentQuestion < questions.length) {
    currentQuestion++;
    answeredQuestions.textContent = currentQuestion + 1;
    skip.style.display = "block";
    displayQuestions();
    masteredAnswers++;
    masteredResult.textContent = masteredAnswers;
    stillLearningResult.textContent = stillLearningAnswers;
  }
  if (currentQuestion >= questions.length) {
    resultBtn.style.display = "block";
    skip.style.display = "none";
    masteredBtn.style.display = "none";
    currentQuestion = questions.length - 1;
    answeredQuestions.textContent = questions.length;
  }

  card.classList.remove("is-flipped");
});

resultBtn.addEventListener("click", () => {
  let questionsAnswered = questions.length - stillLearningAnswers;

  modal.style.display = "flex";
  answerQuestions.textContent = `You answered ${questionsAnswered} from ${questionsNumber} questions`;
  if (masteredAnswers > stillLearningAnswers) {
    resultText.textContent = "Fantastic work! Your knowledge shines bright";
  } else {
    resultText.textContent =
      "Every challenge is a step towards improvement... You've got this!";
  }
  stillLearningResult.textContent = stillLearningAnswers;
  modal.style.background = "rgba(0, 0, 0, 0.2)";
  modal.style.zIndex = "1000";
  container.style.zIndex = "-1000";
});

flashcards.forEach((flashcard) => {
  flashcard.addEventListener("click", function () {
    card.classList.toggle("is-flipped");
  });
});

displayQuestions();

    </script>
</body>
</html>