import sys
import PyPDF2

def extracts(pdf_file: str) -> str:
    try:
        with open(pdf_file, 'rb') as pdf:
            reader = PyPDF2.PdfReader(pdf, strict=False)
            extracted = ''

            for page in reader.pages:
                content = page.extract_text()
                if content:
                    extracted += ' ' + content

            return extracted

    except Exception as e:
        print(str(e), file=sys.stderr)
        return ''

if __name__ == '__main__':
    if len(sys.argv) > 1:
        file_path = '"' + sys.argv[1] + '"'
        pdf_text = extracts(file_path)
        print(pdf_text)
    else:
        print("No data provided for summarization")
