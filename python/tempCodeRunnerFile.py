
def summarize(file_path='/Users/ja/Desktop/untitled folder 2/pathed.txt'):
    # Read the text from the file
    with open(file_path, 'r') as file:
        text = file.read() 
    # Your summarization logic here
    client = OpenAI()
    response = client.chat.completions.create(
    model="gpt-3.5-turbo-1106",
    response_format={ "type": "json_object" },
    messages=[
        {"role": "system", "content": "You are a summarizer designed to output JSON written in this format summarization content (summary) and a score for the quality of the summarization and the coverage of it from 1 to 10 (score)."},
        {"role": "user", "content": text}
    ]
    )
    print(response.choices[0].message.content)