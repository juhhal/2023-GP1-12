import sys
import json
import logging
import tempfile
from openai import OpenAI

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
def generateQuiz(path: str) -> str:
    text = ''
    try:
        logging.info("Reading text from file.")
        with open(path, 'r', encoding='latin-1') as file:
            text = file.read()

        logging.info("Creating OpenAI client and generating response.")
        client = OpenAI(api_key = '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')
        response = client.chat.completions.create(
            model="gpt-3.5-turbo-0125",
            response_format={"type": "json_object"},
            messages=[
                {"role": "system", "content": "You are a multiple choice quiz generator designed to output JSON and never return an empty response, write in this format array of questions (for example: questions) with a length of 10,  and includes the following array: question name (question), 3 multiple choices array (answers), correct choice answer (correctAnswer), and a score for the quality of the question from 1 to 10 (score)."},
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
    if len(sys.argv) > 1:
        file_path = sys.argv[1]
        temp_file_path = generateQuiz(file_path)
        logging.info("Temporary file path: %s", temp_file_path)
        print(temp_file_path)
    else:
        logging.error("No file path provided for generating the quizes.")
