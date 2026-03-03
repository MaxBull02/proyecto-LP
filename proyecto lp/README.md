Proyecto Lenguajes - Pareja 3

Resumen
- Aplicación PHP + PostgreSQL para registro de asistentes y portafolio profesional.
- Servicios en Docker Compose: `db` (Postgres), `web` (PHP/Apache), `logic` (opcional).

Archivos relevantes
- [docker-compose.yml](docker-compose.yml)
- [index.php](index.php) (código principal)
- [db/init.sql](db/init.sql) (migraciones / tablas iniciales)
- [web/Dockerfile](web/Dockerfile) (con pdo_pgsql instalado)
- `web/uploads/` (almacena fotos subidas)

Pasos para ejecutar (en Linux)
1. Asegúrate de tener Docker y Docker Compose instalados.
2. (Opcional) añadir tu usuario al grupo `docker` para evitar sudo:

```bash
sudo usermod -aG docker $USER
# luego cierra sesión y vuelve a entrar
```

3. Construir y levantar los servicios:

```bash
cd "proyecto lp"
docker-compose up -d --build
```

4. Ajustar permisos de uploads (si es necesario):

```bash
docker-compose exec web chown -R www-data:www-data /var/www/html/uploads /var/www/html || true
```

5. Abrir la app en el navegador: http://localhost:8080/

Notas y recomendaciones
- Si el contenedor `web` falla durante la build por prompts de apt, reconstruye con:
  `DEBIAN_FRONTEND=noninteractive docker-compose up -d --build`.
- `db/init.sql` se monta en el init de Postgres y crea las tablas necesarias.
- Yo ya copié `index.php` a `./web/index.php` y creé la carpeta `web/uploads`.

Siguientes pasos que puedo ejecutar por ti
- Ejecutar `docker-compose up -d --build` aquí (necesita acceso al demonio Docker).
- Añadir validaciones adicionales, control CSRF, o interfaz más estética.

Dime si quieres que inicie los contenedores (requiere permisos Docker en este entorno).