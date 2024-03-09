<?php
 session_start(); 
 if (isset($_GET['data'])) {
    $quizesData = json_decode(urldecode($_GET['data']), true);
    $title = urldecode($_GET['title']) ;
    $quizId = urldecode($_GET['id']) ;
 }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="quiz.css" />
     <script src="../jquery.js"></script>

  </head>
  <body>
  <div class="overlay">
      <div class="container">
        <div>
          <div class="title-container">
            <h1 class="title">
              <?php echo $title; ?>
            </h1>
            <a href="/quizes/">
              <img class="close-icon" src="icons/close.svg" alt="" />
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
                    <p class="correct-result">20</p>
                  </div>
                  <p class="info">Correct</p>
                </div>
                <div class="result-card">
                  <div class="result">
                    <div class="wrong"></div>
                    <p class="wrong-result">3</p>
                  </div>
                  <p class="info">Wrong</p>
                </div>
                <div class="result-card">
                  <div class="result">
                    <div class="remaining"></div>
                    <p class="remaining-result">30</p>
                  </div>
                  <p class="info">Remaining</p>
                </div>
              </div>
              <div class="answered-questions">
                <p>1</p>
              </div>
              <a href="/quizes/">
                <button class="back-btn">Back to Home</button>
              </a>
            </div>
          </div>
          <div class="questions-count"></div>
          <div class="question-container">
            <div class="question-progress">
              <p class="">
                <span id="answered-questions">1</span> from
                <span id="questions-number">10</span>
              </p>
            </div>
            <div class="answers-container">
              <div>
                <h2 class="question-text">Question?</h2>
              </div>
              <div class="form" id="form1">
                <div
                  form="form1"
                  class="answer-container"
                  data-correct-answer="answer_1"
                >
                  <input id="answer" name="answerGroup" type="radio" />
                  <label for="answer" class="answer"> Answer One </label>
                </div>
                <div
                  form="form1"
                  class="answer-container"
                  data-correct-answer="answer_2 "
                >
                  <input id="answer1" name="answerGroup" type="radio" />
                  <label for="answer1" class="answer"> Answer Two </label>
                </div>
                <div
                  form="form1"
                  class="answer-container"
                  data-correct-answer="answer_3"
                >
                  <input id="answer2" name="answerGroup" type="radio" />
                  <label for="answer2" class="answer"> Answer Three </label>
                </div>
              </div>
              <div class="skip-container">
                <button class="quiz-back-btn">
                  <img src="../images/arrow-back.svg" alt="" width="20px" />
                </button>
                <p class="skip-btn">Skip</p>
              </div>
              <div class="btn-container">
                <button class="next-btn quiz-btn">Next</button>
                <button class="result-btn quiz-btn">Result</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<script>

const questions = <?php echo json_encode($quizesData['questions']); ?>;

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
const backBtn = document.querySelector('.quiz-back-btn');

let correctAnswers = 0;
let wrongAnswers = 0;
let remainingAnswers = 0;
let currentQuestion = 0;
let questionsNumber = questions.length;
questionNumber.innerText = questions.length;
const questionStatus = questions.map(question => ({
  question: question.question,
  userAnswer: null,
  correct: false,
  status: 'unanswered',
  score: question.score
}));
let questionHistory = [];



const questionColors = [];

for (let i = 0; i < questions.length; i++) {
  questionColors.push("#bf97d8");
}

for (let i = 0; i < questions.length; i++) {
  const questionCountElement = document.createElement("div");
  questionCountElement.classList.add("question-count");
  questionCountContainer.appendChild(questionCountElement);
}

const questionCount = document.querySelectorAll(".question-count");
questionCount[currentQuestion].style.backgroundColor =
  questionColors[currentQuestion];
questionText.innerText = questions[currentQuestion].question;

nextBtn.style.display = "none";

let shuffledAnswers = [];
let answerSelected = false;

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

// Set the userAnswer and status
questionStatus[currentQuestion].userAnswer = selectedAnswer;
if (selectedAnswer === correctAnswer) {
  correctAnswers++;
  correctResult.textContent = correctAnswers;
  questionStatus[currentQuestion].status = 'correct';
  questionStatus[currentQuestion].correct = true;
} else {
  wrongAnswers++;
  wrongResult.textContent = wrongAnswers;
  questionStatus[currentQuestion].status = 'incorrect';
}

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

function restoreAnswerState() {
  const currentQuestionStatus = questionStatus[currentQuestion];
  const correctAnswer = questions[currentQuestion].correctAnswer;
  const userAnswer = currentQuestionStatus.userAnswer;

  answerContainers.forEach((container) => {
    const answerText = container.innerText;

    if (answerText === userAnswer) {
      container.style.backgroundColor =
        userAnswer === correctAnswer ? "#F1F9EB" : "#FEECEC";
      container.style.border =
        userAnswer === correctAnswer
          ? "1px solid #88CE57"
          : "1px solid #F86363";

    } else {
      container.style.backgroundColor = "#ffffff";
      container.style.border = "none";
    }
  });

  if (currentQuestionStatus.status === 'correct') {
    questionCount[currentQuestion].style.backgroundColor = "#88CE57";
  } else if (currentQuestionStatus.status === 'incorrect') {
    questionCount[currentQuestion].style.backgroundColor = "#F86363";
  } else {
    questionCount[currentQuestion].style.backgroundColor = "#bf97d8";
  }

}


function displayQuestions() {
  if (currentQuestion >= questions.length) {
    return;
  }

  const correctAnswer = questions[currentQuestion].correctAnswer;
  const falseAnswers = questions[currentQuestion].answers.filter(
    (answer) => answer !== correctAnswer
  );

  shuffledAnswers = shuffleAnswers(correctAnswer, falseAnswers);

  answerContainers.forEach((label, index) => {
    label.innerText = shuffledAnswers[index];

    // if the answer is empty or undefined hide the answer container
    if (shuffledAnswers[index] === "" || shuffledAnswers[index] === undefined) {
      label.style.display = "none";
    } else {
      label.style.display = "block";
    }
  });

  questionText.innerText = questions[currentQuestion].question;
  questionCount[currentQuestion].style.backgroundColor =
    questionColors[currentQuestion];
}

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
  if (currentQuestion < questions.length) {

    questionStatus[currentQuestion].status = 'skipped';
    questionCount[currentQuestion].style.backgroundColor = "#6766661F";
    
    remainingAnswers++;
    remainingResult.textContent = remainingAnswers;

    answerContainers.forEach((container) => {
      container.style.backgroundColor = "#ffffff";
      container.style.border = "none";
    });

    currentQuestion++;

    answerSelected = false;

    answeredQuestions.textContent =
      currentQuestion === questions.length
        ? questions.length
        : currentQuestion + 1;

    handleResultBtn();
    displayQuestions();

    nextBtn.style.display = "none";

    if (currentQuestion < questions.length) {
      skip.style.display = "block";
    } else {
      skip.style.display = "none";
    }
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

  questionHistory.push(currentQuestion);
  handleBackBtnVisibility();
});

backBtn.addEventListener('click', () => {
  questionHistory.pop();
  currentQuestion = questionHistory.length > 0 ? questionHistory[questionHistory.length - 1] : 0;
  handleBackBtnVisibility();
  displayQuestions();
  restoreAnswerState();
});



resultBtn.addEventListener("click",async () => {
  await $.ajax({
    url: 'postQuizResult.php',
    method: 'POST',
    data: {
      quizId:'<?php echo $quizId; ?>',
      result: questionStatus
    },
    success: function(res) {
      console.log(res);
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error("Error:", textStatus, errorThrown);
    }
  });

  const unansweredQuestionCount = questionStatus.filter(
    (question) => question.status === 'unanswered'
  ).length;
  const correctAnswerCount = questionStatus.filter(
    (question) => question.status === 'correct'
  ).length;
  const wrongAnswerCount = questionStatus.filter(
    (question) => question.status === 'incorrect'
  ).length;

  wrongResult.textContent = wrongAnswerCount;
  correctResult.textContent = correctAnswerCount;
  remainingResult.textContent = unansweredQuestionCount;
  modal.style.display = "flex";
  answerQuestions.textContent = `You answered ${
    correctAnswerCount + wrongAnswerCount
  } from ${questionsNumber} questions`;
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

function handleBackBtnVisibility() {
  // If there is no history, disable or hide the back button
  if (questionHistory.length === 0) {
    backBtn.style.display = 'none';
  } else {
    backBtn.style.display = 'block';
  }
}


handleBackBtnVisibility();
displayQuestions();
</script>
  </body>
</html>
