# OCR + Face Verification Service

Service ini dipakai Laravel untuk memverifikasi upload KTP dan selfie pada flow rental.

## Requirements

- Python 3.10 atau 3.11
- `pip`

Python 3.12+ belum direkomendasikan untuk service ini karena beberapa dependency utama
(`deepface`, `tensorflow`, dan sebagian stack OCR) belum konsisten di semua mesin.

## Setup

1. Masuk ke folder service:

   ```bash
   cd ai/face
   ```

2. Buat virtual environment:

   ```bash
   python3 -m venv .venv
   ```

   Pastikan `python3 --version` menunjukkan `3.10.x` atau `3.11.x`.

3. Aktifkan environment:

   ```bash
   source .venv/bin/activate
   ```

4. Install dependencies:

   ```bash
   pip install --upgrade pip setuptools wheel
   pip install -r requirements.txt
   ```

5. Jalankan service:

   ```bash
   python app.py
   ```

Service akan berjalan di:

```text
http://127.0.0.1:5000
```

## Laravel Integration

Set environment Laravel agar mengarah ke service ini:

```env
FACE_VERIFY_BASE_URL=http://127.0.0.1:5000
FACE_VERIFY_TIMEOUT=60
```

## Endpoint

### `POST /verify`

Form-data yang dibutuhkan:

- `ktp`: file gambar KTP
- `selfie`: file gambar selfie user

Contoh response sukses:

```json
{
  "nik": "1234567890123456",
  "raw_text": "...",
  "clean_text": "...",
  "face_match": true,
  "distance": 0.21,
  "verified": true
}
```

`verified` hanya bernilai `true` jika:

- NIK berhasil terbaca dari OCR, dan
- wajah pada KTP cocok dengan selfie
