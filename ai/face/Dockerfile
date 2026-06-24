FROM python:3.10-slim

WORKDIR /app

# Install system dependencies required for OpenCV, TensorFlow, and PyTorch/docTR
RUN apt-get update && apt-get install -y --no-install-recommends \
    build-essential \
    libgl1 \
    libglib2.0-0 \
    libgomp1 \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements file first to utilize Docker build cache
COPY requirements.txt .

# Install python dependencies:
# 1. Install CPU-only PyTorch first to avoid heavy CUDA wheels (saves ~5GB disk space)
# 2. Install the rest of the requirements
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir torch==2.5.1 torchvision==0.20.1 --index-url https://download.pytorch.org/whl/cpu && \
    pip install --no-cache-dir -r requirements.txt

# Copy application source code
COPY . .

# Create directory for uploads (mounted or internal)
RUN mkdir -p uploads && chmod 777 uploads

EXPOSE 3000

ENV FLASK_APP=app.py
ENV PYTHONUNBUFFERED=1

CMD ["python", "app.py"]
