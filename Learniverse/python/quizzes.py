import sys
import json
import logging
import tempfile
from openai import OpenAI
import PyPDF2
import docx

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
def generateQuiz(path: str, quizType: str) -> str:
    text = ''
    try:
        logging.info("Reading text from file.")
        if path.endswith('.pdf'):
            with open(path, 'rb') as pdf:
                reader = PyPDF2.PdfReader(pdf, strict=False)
                extracted = ''
                for page in reader.pages:
                    content = page.extract_text()
                    if content:
                        extracted += ' ' + content
                text = extracted
        elif path.endswith('.docx'):
            doc = docx.Document(path)
            extracted = ' '.join([paragraph.text for paragraph in doc.paragraphs])
            text = extracted
        else:
            logging.error("Unsupported file type. Only PDF and DOCX files are supported.")
            return ''

        if quizType == "questionAnswers":
            system_prompt = "You are a multiple choice quiz generator designed to output JSON and never return an empty response, write in this format array of questions (for example: questions) with a length of 10,  and includes the following array: question name (question), 3 multiple choices array (answers), correct choice answer (correctAnswer), and a score for the quality of the question from 1 to 10 (score)."
        elif quizType == "trueFalse":
            system_prompt = "Generate a JSON-formatted true or false quiz with the following specifications in one string: The output should be an object containing a key 'questions' with its value being an array of 10 objects. Each object must include: 'question' (string), 'correctAnswer' (a string of either 'true' or 'false' only), 'answers' (an array with two elements: ['true', 'false'] only), and 'score' (an integer from 1 to 10 indicating the question's quality). Ensure questions vary in difficulty and are non-repetitive, with no empty responses."
        elif quizType == "mixed":
            system_prompt = """"
            Generate a JSON-formatted mixed quiz (including both multiple choice and true/false questions) with the following specifications in a single string:
            The output should be an object containing a key 'questions' with its value being an array of 10 objects. 
            Each object must include: 'question' (a string), 'answers' (an array, which will contain either 3 multiple choice answers or ['true', 'false'] for true/false questions), 'correctAnswer' (the correct choice answer as a string, which will be one of the items from 'answers'), and 'score' (an integer from 1 to 10 indicating the question's quality).
            Ensure questions vary in difficulty, cover a range of topics, and are non-repetitive, with no empty responses."""
            

        logging.info("Creating OpenAI client and generating response.",)
        client = OpenAI(api_key = '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')
        response = client.chat.completions.create(
            model="gpt-3.5-turbo-0125",
            response_format={"type": "json_object"},
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": text}
            ]
        )

        logging.info("Writing response to temporary file." + response.choices[0].message.content)
        temp_file = tempfile.NamedTemporaryFile(delete=False)
        with open(temp_file.name, 'w') as file:
            file.write(response.choices[0].message.content)

        logging.info("Quizes Generatation is complete.")
        return temp_file.name
    
    except Exception as e:
        logging.error("An error occurred during generating the quizes: %s", str(e))
        return ''

if __name__ == "__main__":
    if len(sys.argv) > 2:
        file_path = sys.argv[1]
        quiz_type = sys.argv[2]
        temp_file_path = generateQuiz(file_path, quiz_type)
        logging.info("Temporary file path: %s", temp_file_path)
        print(temp_file_path)
    else:
        logging.error("Insufficient arguments provided for generating the quizzes.")
