<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="quiz.css" />
  </head>
  <body>
    <div class="overlay">
      <div class="container">
        <div>
          <div class="title-container">
            <h1 class="title">Title</h1>
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
    
    <script src="quiz.js"></script>
  </body>
</html>
