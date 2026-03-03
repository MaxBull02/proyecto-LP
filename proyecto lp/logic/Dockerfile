FROM python:3.11-slim
WORKDIR /app
# Instalamos dependencias para Postgres
RUN apt-get update && apt-get install -y libpq-dev gcc && \
    pip install psycopg2-binary
COPY script.py .
CMD ["python", "script.py"]