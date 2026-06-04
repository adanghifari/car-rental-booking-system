from flask import Flask, request, jsonify
from deepface import DeepFace
from doctr.io import DocumentFile
from doctr.models import ocr_predictor

import re
import uuid
import os

app = Flask(__name__)

# =========================
# OCR MODEL
# =========================
ocr_model = ocr_predictor(
    det_arch="db_resnet50",
    reco_arch="crnn_vgg16_bn",
    pretrained=True
)

UPLOAD_DIR = "uploads"
os.makedirs(UPLOAD_DIR, exist_ok=True)


# =========================
# SAVE FILE
# =========================
def save_file(file):
    ext = file.filename.split('.')[-1]
    filename = f"{uuid.uuid4()}.{ext}"
    path = os.path.join(UPLOAD_DIR, filename)
    file.save(path)
    return path


# =========================
# OCR
# =========================
def extract_text(image_path):
    doc = DocumentFile.from_images(image_path)
    result = ocr_model(doc)

    lines = []

    for page in result.pages:
        for block in page.blocks:
            for line in block.lines:
                text = " ".join([w.value for w in line.words])
                if text.strip():
                    lines.append(text)

    return " ".join(lines)


# =========================
# CLEAN TEXT
# =========================
def clean_text(text):
    return re.sub(r'\s+', ' ', text.upper()).strip()


# =========================
# EXTRACT NIK
# =========================
def extract_nik(text):
    match = re.search(r'\d{16}', text)
    return match.group(0) if match else None


# =========================
# API
# =========================
@app.route('/verify', methods=['POST'])
def verify():
    if 'ktp' not in request.files or 'selfie' not in request.files:
        return jsonify({"error": "ktp and selfie required"}), 400

    try:
        ktp_path = save_file(request.files['ktp'])
        selfie_path = save_file(request.files['selfie'])

        # =========================
        # OCR (RAW IMAGE ONLY)
        # =========================
        raw_text = extract_text(ktp_path)
        clean = clean_text(raw_text)
        nik = extract_nik(clean)

        # =========================
        # FACE VERIFY
        # =========================
        face = DeepFace.verify(
            img1_path=ktp_path,
            img2_path=selfie_path,
            model_name="VGG-Face",
            detector_backend="retinaface",
            enforce_detection=False
        )

        return jsonify({
            "nik": nik,
            "raw_text": raw_text,
            "clean_text": clean,
            "face_match": face["verified"],
            "distance": float(face["distance"]),
            "verified": nik is not None and face["verified"]
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 500


if __name__ == "__main__":
    app.run(port=5000, debug=True)
