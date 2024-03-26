import sys
import json
import tempfile
import logging
from openai import OpenAI

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def generate(tempFilePath: str, start: str, end: str, previousEvents: str) -> str:
    try:
        
        with open(tempFilePath, 'r', encoding='latin-1') as file:
            text = file.read()
        previousEvents = json.loads(previousEvents)
        
        logging.info(f"Creating OpenAI client and generating response with {start} to {end} including {previousEvents} and {text}.")
        client = OpenAI(api_key = '')
        response = client.chat.completions.create(
            model="gpt-3.5-turbo-0125",
            response_format={"type": "json_object"},
            messages= [
                {
                    "role": "system",
                    "content": f"As a study planner, your task is to create a study plan by dividing all the main topics from the user content without changing their order, every new lecture has (warning new lecture) flag, considering my calendar events for the dates: from {start} to {end}. Please ensure that the study plan takes into account the existing calendar events but does not include them in the response:{previousEvents}. count how many events are for each day and Distribute the topics in a way that days with higher events count have less work, and each day must have only one study plan do not leave empty days. Provide the response in JSON format following this structure: 'study_plan(date, title, description)' where 'date' represents the study date, 'title' is the title of the study plan, and 'description' one sentence that explains the main topics for that day."
                },
                {
                    "role": "user",
                    "content": text
                }
            ]
            

        )

        logging.info("Writing response to temporary file." + response.choices[0].message.content)
        temp_file = tempfile.NamedTemporaryFile(delete=False)
        with open(temp_file.name, 'w') as file:
            file.write(response.choices[0].message.content)

        logging.info("planning complete.")
        return temp_file.name

    except Exception as e:
        logging.error("An error occurred during summarization: %s", str(e))
        return ''

if __name__ == "__main__":
    if len(sys.argv) != 5:
        print("Usage: python StudyPlanner.py <tempFilePath> <start> <end> <previousEvents>")
    else:
        tempFilePath = sys.argv[1]
        start = sys.argv[2]
        end = sys.argv[3]
        previousEvents = sys.argv[4]
        
        # Call generate function with the arguments
        response = generate(tempFilePath, start, end, previousEvents)
        print(response)