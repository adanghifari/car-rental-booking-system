from flask import Flask, request, jsonify
from deepface import DeepFace
from doctr.io import DocumentFile
from doctr.models import ocr_predictor

import re
import sys
import uuid
import os
import logging

# if sys.version_info < (3, 10) or sys.version_info >= (3, 12):
#     raise RuntimeError("This service requires Python 3.10 or 3.11.")

app = Flask(__name__)
logger = logging.getLogger(__name__)

# Configuration
ALLOWED_EXTENSIONS = {'jpg', 'jpeg', 'png'}
MAX_FILE_SIZE = 5 * 1024 * 1024  # 5MB

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


def validate_image_file(file):
    """Validate uploaded image file"""
    if not file or not file.filename:
        return False, "No file provided"
    
    ext = file.filename.split('.')[-1].lower()
    if ext not in ALLOWED_EXTENSIONS:
        return False, f"Invalid file format. Allowed: {', '.join(ALLOWED_EXTENSIONS)}"
    
    file.seek(0, 2)  # Seek to end
    file_size = file.tell()
    file.seek(0)  # Reset to start
    
    if file_size > MAX_FILE_SIZE:
        return False, f"File too large. Max {MAX_FILE_SIZE / 1024 / 1024}MB"
    
    if file_size < 100:  # Minimum reasonable image size
        return False, "File too small"
    
    return True, None


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


def extract_nik(text):
    """Extract and validate Indonesian NIK from text"""
    # Remove all non-digit characters
    digits_only = re.sub(r'\D', '', text)
    
    # Find all 16-digit sequences
    matches = re.findall(r'\d{16}', digits_only)
    
    if not matches:
        return None
    
    # Use first match and validate NIK format
    nik = matches[0]
    
    if is_valid_nik(nik):
        return nik
    
    return None


def is_valid_nik(nik):
    """Bypass check: Cukup pastikan panjangnya 16 digit angka"""
    return nik is not None and len(nik) == 16 and nik.isdigit()


# =========================
# API
# =========================
@app.route('/verify', methods=['POST'])
def verify():
    """Verify KTP identity and match with selfie using OCR + face detection"""
    ktp_path = None
    selfie_path = None
    
    try:
        # Validate request has required files
        if 'ktp' not in request.files or 'selfie' not in request.files:
            return jsonify({"error": "ktp and selfie required"}), 400
        
        ktp_file = request.files['ktp']
        selfie_file = request.files['selfie']
        
        # Validate both files
        ktp_valid, ktp_error = validate_image_file(ktp_file)
        if not ktp_valid:
            return jsonify({"error": f"ktp: {ktp_error}"}), 400
        
        selfie_valid, selfie_error = validate_image_file(selfie_file)
        if not selfie_valid:
            return jsonify({"error": f"selfie: {selfie_error}"}), 400
        
        # Save files
        ktp_path = save_file(ktp_file)
        selfie_path = save_file(selfie_file)
        
        # Extract text from KTP using OCR
        raw_text = extract_text(ktp_path)
        clean = clean_text(raw_text)
        nik = extract_nik(clean)
        
        if not nik:
            logger.warning("Failed to extract valid NIK from KTP")
            return jsonify({
                "verified": False,
                "nik": None,
                "error": "Could not extract valid NIK from KTP",
                "raw_text": raw_text[:500]  # Return truncated text for debugging
            }), 200
        
        # Perform face verification
        try:
            face_result = DeepFace.verify(
                img1_path=ktp_path,
                img2_path=selfie_path,
                model_name="VGG-Face",
                detector_backend="retinaface",
                enforce_detection=False
            )

            print(f"Face verification result: {face_result}")
            
            # Check face match with confidence threshold
            face_verified = face_result["verified"]

            print({
                "nik": nik,
                "raw_text": raw_text,
                "clean_text": clean,
                "face_match": face_result["verified"],
                "distance": float(face_result["distance"]),
                "verified": nik is not None and face_verified
            })
            
            return jsonify({
                "nik": nik,
                "raw_text": raw_text,
                "clean_text": clean,
                "face_match": face_result["verified"],
                "distance": float(face_result["distance"]),
                "verified": nik is not None and face_verified
            }), 200
        
        except Exception as face_error:
            logger.error(f"Face verification failed: {str(face_error)}")
            return jsonify({
                "verified": False,
                "nik": nik,
                "error": f"Face verification failed: {str(face_error)}"
            }), 200
    
    except FileNotFoundError as e:
        logger.error(f"File not found: {str(e)}")
        return jsonify({"error": "invalid_image"}), 400
    
    except Exception as e:
        logger.error(f"Verification error: {str(e)}")
        return jsonify({"error": "verification_failed"}), 500
    
    finally:
        # Cleanup uploaded files
        if ktp_path and os.path.exists(ktp_path):
            try:
                os.remove(ktp_path)
            except Exception as e:
                logger.warning(f"Failed to cleanup KTP file: {str(e)}")
        
        if selfie_path and os.path.exists(selfie_path):
            try:
                os.remove(selfie_path)
            except Exception as e:
                logger.warning(f"Failed to cleanup selfie file: {str(e)}")


if __name__ == "__main__":
    app.run(host="0.0.0.0", port=3000, debug=True)
