import psycopg2
import time

def generar_reporte():
    try:
        # Conexión a la DB usando el nombre 'db' del compose
        conn = psycopg2.connect(
            host="db",
            database="asistencia_db",
            user="user_p3",
            password="password123"
        )
        cur = conn.cursor()
        
        # Requerimiento Pareja 3: Contar asistentes
        cur.execute("SELECT COUNT(*) FROM asistentes;")
        total = cur.fetchone()[0]
        
        # Escribir en el volumen compartido
        with open("/app/reports/reporte.txt", "w") as f:
            f.write(f"Total de estudiantes pre-registrados: {total}\n")
            f.write(f"Reporte generado el: {time.ctime()}\n")
            
        cur.close()
        conn.close()
    except Exception as e:
        print(f"Error en Capa Logica: {e}")

if __name__ == "__main__":
    while True:
        generar_reporte()
        time.sleep(30) # Actualiza cada 30 segundos