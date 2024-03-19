import sys
import json
import logging
import tempfile
from openai import OpenAI
import os
import openai

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def summarize(path: str) -> str:
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
                {"role": "system", "content": "You are a summarizer designed to output JSON with all the summary written inside this key (summary) and never return an empty response."},
                {"role": "user", "content": text}
            ]
        )

        logging.info("Writing response to temporary file." + response.choices[0].message.content)
        temp_file = tempfile.NamedTemporaryFile(delete=False)
        with open(temp_file.name, 'w') as file:
            file.write(response.choices[0].message.content)

        logging.info("Summarization complete.")
        return temp_file.name
    
    except Exception as e:
        logging.error("An error occurred during summarization: %s", str(e))
        return ''

if __name__ == "__main__":
    if len(sys.argv) > 1:
        file_path = sys.argv[1]
        temp_file_path = summarize(file_path)
        logging.info("Temporary file path: %s", temp_file_path)
        print(temp_file_path)
    else:
        logging.error("No file path provided for summarization.")
