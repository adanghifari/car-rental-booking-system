#!/bin/bash
set -e
cd "/mnt/d/vscode/SEMESTER 6/COMPRO/car-rental-booking-system/ai/face"

if [ ! -x .venv/bin/python3 ]; then
  echo "Creating Python 3.11 virtual environment..."
  python3.11 -m venv .venv
fi

. .venv/bin/activate
python -m pip install --upgrade pip setuptools wheel
python -m pip install tf-keras
python -c 'from deepface import DeepFace; print("DeepFace OK")'
