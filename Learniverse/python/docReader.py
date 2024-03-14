import sys
from docx import Document

def extract_text_from_docx(docx_file: str) -> str:
    try:
        doc = Document(docx_file)
        extracted_text = []

        for para in doc.paragraphs:
            extracted_text.append(para.text)
        
        return '\n'.join(extracted_text)

    except Exception as e:
        print(str(e), file=sys.stderr)
        return ''

if __name__ == '__main__':
    if len(sys.argv) > 1:
        file_path = sys.argv[1]
        docx_text = extract_text_from_docx(file_path)
        print(docx_text)
    else:
        print("Please provide a DOCX file path.")
