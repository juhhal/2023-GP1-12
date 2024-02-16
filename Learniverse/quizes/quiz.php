<?php

 session_start(); 
 if (isset($_GET['data'])) {
    // Decode the JSON string into a PHP array
    $arrayData = json_decode(urldecode($_GET['data']), true);
    var_dump($arrayData);
 } 
?>
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
          <h1 class="title">Quiz</h1>
            <a href="index.php">
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
          <?php
         
// Function to generate HTML for a single question
function generateQuestionHTML($questionData) {
  $html = '<div class="question-container">';
  $html .= '<div class="question-progress">';
  $html .= '<p><span id="answered-questions">1</span> from <span id="questions-number">1</span></p>';
  $html .= '</div>';
  $html .= '<div class="answers-container">';
  $html .= '<div>';
  $html .= '<h2 class="question-text">' . $questionData['question'] . '</h2>';
  $html .= '</div>';
  $html .= '<div class="form" id="form1">';

  foreach ($questionData['choices'] as $choiceIndex => $choice) {
      $html .= '<div form="form1" class="answer-container" data-correct-answer="answer_' . $questionData['correct'] . '">';
      $html .= '<input id="answer' . $choiceIndex . '" name="answerGroup" type="radio" />';
      $html .= '<label for="answer' . $choiceIndex . '" class="answer">' . $choice . '</label>';
      $html .= '</div>';
  }

  $html .= '</div>';
  $html .= '<div class="skip-container">';
  $html .= '<p class="skip-btn">Skip</p>';
  $html .= '</div>';
  $html .= '<div class="btn-container">';
  $html .= '<button class="next-btn quiz-btn">Next</button>';
  $html .= '<button class="result-btn quiz-btn">Result</button>';
  $html .= '</div>';
  $html .= '</div>';
  $html .= '</div>';

  return $html;
}

// Display the first question initially
echo generateQuestionHTML($arrayData['success']['question1']);
?>

        </div>
      </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const questions = document.querySelectorAll('.question-container');
        let currentQuestionIndex = 0;

        function showQuestion(index) {
            questions.forEach((question, i) => {
                question.style.display = i === index ? 'block' : 'none';
            });
        }

        // Show the first question initially
        showQuestion(currentQuestionIndex);

        // Add event listener for the next button
        document.querySelector('.next-btn').addEventListener('click', function () {
            currentQuestionIndex++;
            if (currentQuestionIndex < questions.length) {
                showQuestion(currentQuestionIndex);
            }
        });

        // Add event listener for the skip button
        document.querySelector('.skip-btn').addEventListener('click', function () {
            currentQuestionIndex++;
            if (currentQuestionIndex < questions.length) {
                showQuestion(currentQuestionIndex);
            }
        });
    });
</script>
<script>
window.onload = function() {
    var storedData = localStorage.getItem('quizData');

    if (storedData) {
        var quizData = JSON.parse(storedData);
        console.log(quizData);
    } else {
        console.error('No quiz data found in local storage.');
        // Handle the absence of data appropriately
    }

    // Optional: Clear the quizData from local storage if it's no longer needed
    localStorage.removeItem('quizData');

}
</script>

    <script src="quiz.js"></script>
  </body>
</html>
