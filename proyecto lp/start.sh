#!/usr/bin/env bash
set -euo pipefail

# start.sh - prepara el proyecto y arranca los contenedores
# Uso: ./start.sh

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

echo "[start.sh] Directorio del proyecto: $PROJECT_DIR"

echo "[start.sh] Creando carpetas necesarias..."
mkdir -p "web/uploads" "shared" "reports" "db"

if [ -f index.php ] && [ ! -f web/index.php ]; then
  echo "[start.sh] Copiando index.php a web/index.php"
  cp index.php web/index.php
fi

echo "[start.sh] Estableciendo permisos locales en uploads..."
chmod 755 web/uploads || true

echo "[start.sh] Levantando contenedores (build)..."
DEBIAN_FRONTEND=noninteractive docker-compose up -d --build || {
  echo "[start.sh] docker-compose falló, intentando con sudo..."
  sudo DEBIAN_FRONTEND=noninteractive docker-compose up -d --build
}

echo "[start.sh] Esperando 3s para que los servicios inicien..."
sleep 3

echo "[start.sh] Verificando servicios en ejecución..."
docker-compose ps --services --filter "status=running" || true

echo "[start.sh] Ajustando permisos dentro del contenedor web (si existe)..."
if docker-compose ps --services --filter "status=running" | grep -q "web"; then
  docker-compose exec web chown -R www-data:www-data /var/www/html/uploads /var/www/html || true
fi

echo "[start.sh] Listo. Abre http://localhost:8080/"

exit 0
